<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $user = Auth::user();
            if ($user->is_banned) {
                Auth::logout();
                return back()->withErrors(['phone' => __('auth.account_banned')]);
            }
            if ($user->is_suspended) {
                Auth::logout();
                return back()->withErrors(['phone' => __('auth.account_suspended')]);
            }
            $request->session()->regenerate();
            return redirect()->intended(route('browse.index'));
        }

        return back()->withErrors(['phone' => __('auth.failed')])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
