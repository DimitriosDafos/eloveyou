<?php
namespace App\Http\Controllers;

use App\Models\Practice;
use App\Services\ProfileCompletenessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function setup(ProfileCompletenessService $completeness)
    {
        $user = Auth::user();
        $practices = Practice::orderBy('sort_order')->get();
        $missing = $completeness->missingFields($user);
        return view('profile.setup', compact('user', 'practices', 'missing'));
    }

    public function update(Request $request, ProfileCompletenessService $completeness)
    {
        $user = Auth::user();
        $data = $request->validate([
            'age'             => 'required|integer|min:18|max:120',
            'gender'          => 'required|string|max:50',
            'looking_for'     => 'required|array|min:1',
            'looking_for.*'   => 'string|max:50',
            'location_city'   => 'required|string|max:100',
            'location_region' => 'required|string|max:100',
            'bio'             => ['required','string','min:100','max:3000', function($attr,$val,$fail){
                if (preg_match('/[^\x{0020}-\x{007E}\x{00A0}-\x{024F}\s]/u', $val)) $fail(__('profile.no_emojis'));
            }],
            'practices'       => 'required|array|min:1',
            'practices.*'     => 'exists:practices,id',
        ]);

        $user->update([
            'age'             => $data['age'],
            'gender'          => $data['gender'],
            'looking_for'     => $data['looking_for'],
            'location_city'   => $data['location_city'],
            'location_region' => $data['location_region'],
            'bio'             => $data['bio'],
        ]);

        $user->practices()->sync($data['practices']);
        $user->update(['profile_complete' => $completeness->isComplete($user)]);

        return redirect()->route('profile.photos')->with('success', __('profile.saved'));
    }

    public function photos() { return view('profile.photos', ['user' => Auth::user()]); }

    public function uploadPhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048']);
        $user = Auth::user();

        if ($user->photos()->count() >= 5) {
            return back()->withErrors(['photo' => __('profile.max_photos')]);
        }

        $path = $request->file('photo')->store("photos/{$user->id}", 'public');
        $isPrimary = $user->photos()->count() === 0;

        $user->photos()->create([
            'path'       => $path,
            'is_primary' => $isPrimary,
            'sort_order' => $user->photos()->count(),
            'status'     => 'pending',
        ]);

        return back()->with('success', __('profile.photo_uploaded'));
    }

    public function deletePhoto(Request $request, $photoId)
    {
        $user = Auth::user();
        $photo = $user->photos()->findOrFail($photoId);
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return back()->with('success', __('profile.photo_deleted'));
    }

    public function toggleIncognito(Request $request)
    {
        $user = Auth::user();
        $user->update(['is_incognito' => !$user->is_incognito]);
        return back()->with('success', $user->is_incognito ? __('profile.incognito_on') : __('profile.incognito_off'));
    }

    public function destroy(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);
        $user = Auth::user();
        Auth::logout();
        $user->photos->each(fn($p) => Storage::disk('public')->delete($p->path));
        $user->delete();
        $request->session()->invalidate();
        return redirect('/')->with('success', __('profile.account_deleted'));
    }
}
