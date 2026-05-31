<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showStep1() { return view('auth.register-step1'); }

    public function postStep1(Request $request, SmsService $sms)
    {
        $data = $request->validate([
            'username'    => 'required|string|min:3|max:30|unique:users|alpha_dash',
            'real_name'   => 'required|string|min:2|max:100',
            'phone'       => 'required|string|min:7|max:20|unique:users',
            'password'    => 'required|string|min:8|confirmed',
            'age_confirm' => 'accepted',
        ]);

        $code = $sms->generateCode();
        $user = User::create([
            'username'   => $data['username'],
            'real_name'  => $data['real_name'],
            'phone'      => $data['phone'],
            'password'   => Hash::make($data['password']),
            'age_confirmed' => true,
            'phone_verification_code' => Hash::make($code),
            'phone_code_expires_at'   => Carbon::now()->addMinutes(10),
            'locale' => app()->getLocale(),
        ]);

        $sms->sendVerificationCode($data['phone'], $code);
        session(['pending_user_id' => $user->id]);

        return redirect()->route('register.verify');
    }

    public function showVerify() { return view('auth.verify-phone'); }

    public function postVerify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        $userId = session('pending_user_id');
        $user = User::findOrFail($userId);

        if ($user->phone_code_expires_at->isPast()) {
            return back()->withErrors(['code' => __('auth.code_expired')]);
        }
        if (!Hash::check($request->code, $user->phone_verification_code)) {
            return back()->withErrors(['code' => __('auth.code_invalid')]);
        }

        $user->update([
            'phone_verified_at' => now(),
            'phone_verification_code' => null,
            'phone_code_expires_at' => null,
        ]);

        Auth::login($user);
        session()->forget('pending_user_id');

        return redirect()->route('profile.setup')->with('success', __('auth.phone_verified'));
    }

    public function resendCode(Request $request, SmsService $sms)
    {
        $userId = session('pending_user_id');
        $user = User::findOrFail($userId);
        $code = $sms->generateCode();
        $user->update([
            'phone_verification_code' => Hash::make($code),
            'phone_code_expires_at'   => Carbon::now()->addMinutes(10),
        ]);
        $sms->sendVerificationCode($user->phone, $code);
        return back()->with('success', __('auth.code_resent'));
    }
}
