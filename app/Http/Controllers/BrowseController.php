<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Block;
use App\Models\Report;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $blockedIds = Block::where('blocker_id', $user->id)->pluck('blocked_id')
            ->merge(Block::where('blocked_id', $user->id)->pluck('blocker_id'))
            ->unique()->toArray();

        $query = User::visible()
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $blockedIds);

        if ($request->age_group && $request->age_group !== 'any') {
            [$min, $max] = match($request->age_group) {
                '18-25' => [18, 25],
                '25-35' => [25, 35],
                '35-45' => [35, 45],
                '45-55' => [45, 55],
                '55+'   => [55, 120],
                default => [18, 120],
            };
            $query->whereBetween('age', [$min, $max]);
        }

        if ($request->practice) {
            $query->whereHas('practices', fn($q) => $q->where('practices.id', $request->practice));
        }

        $profiles = $query->with(['photos' => fn($q) => $q->where('status','approved')])->paginate(12);
        $practices = \App\Models\Practice::orderBy('sort_order')->get();
        return view('browse.index', compact('profiles', 'practices'));
    }

    public function show(string $username)
    {
        $user = auth()->user();
        $profile = User::where('username', $username)->where('profile_complete', true)->firstOrFail();

        if ($user->hasBlocked($profile) || $user->isBlockedBy($profile)) abort(404);

        $existingMatch = MatchModel::where(function($q) use ($user, $profile) {
            $q->where('requester_id', $user->id)->where('acceptor_id', $profile->id);
        })->orWhere(function($q) use ($user, $profile) {
            $q->where('requester_id', $profile->id)->where('acceptor_id', $user->id);
        })->first();

        return view('browse.profile', compact('profile', 'existingMatch'));
    }

    public function block(Request $request, int $userId)
    {
        $blocked = User::findOrFail($userId);
        Block::firstOrCreate(['blocker_id' => auth()->id(), 'blocked_id' => $blocked->id]);
        return back()->with('success', __('browse.user_blocked'));
    }

    public function report(Request $request, int $userId)
    {
        $request->validate(['type' => 'required|in:profile,photo,message', 'description' => 'nullable|string|max:500']);
        Report::create([
            'reporter_id'      => auth()->id(),
            'reported_user_id' => $userId,
            'type'             => $request->type,
            'description'      => $request->description,
        ]);
        return back()->with('success', __('browse.report_sent'));
    }
}
