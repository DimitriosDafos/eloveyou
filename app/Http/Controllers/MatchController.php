<?php
namespace App\Http\Controllers;
use App\Models\UserMatch;
use App\Models\Chat;
use App\Models\User;
use App\Services\MessageFilterService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pending = UserMatch::where('acceptor_id', $user->id)->where('status','pending')
            ->with('requester')->latest()->get();
        $sent = UserMatch::where('requester_id', $user->id)->where('status','pending')
            ->with('acceptor')->latest()->get();
        return view('matches.index', compact('pending', 'sent'));
    }

    public function send(Request $request, int $userId, MessageFilterService $filter)
    {
        $request->validate(['message' => 'required|string|min:10|max:500']);
        $user = auth()->user();
        $target = User::findOrFail($userId);

        if (UserMatch::where('requester_id', $user->id)->where('acceptor_id', $userId)->exists()) {
            return back()->withErrors(['message' => __('match.already_sent')]);
        }

        $result = $filter->filterMessage($request->message);
        if ($result['blocked']) {
            return back()->withErrors(['message' => __('match.filter_blocked')])->with('filter_message', true);
        }

        UserMatch::create([
            'requester_id'   => $user->id,
            'acceptor_id'    => $userId,
            'opening_message'=> $request->message,
            'status'         => 'pending',
        ]);

        return back()->with('success', __('match.sent'));
    }

    public function accept(Request $request, int $id)
    {
        $match = UserMatch::where('acceptor_id', auth()->id())->where('id', $id)->where('status','pending')->firstOrFail();
        $match->update(['status' => 'accepted', 'accepted_at' => now()]);
        $chat = Chat::create([
            'match_id'     => $match->id,
            'requester_id' => $match->requester_id,
            'acceptor_id'  => $match->acceptor_id,
            'expires_at'   => now()->addHours(24),
        ]);
        return redirect()->route('payment.chat', $chat->id);
    }

    public function decline(Request $request, int $id)
    {
        $match = UserMatch::where('acceptor_id', auth()->id())->where('id', $id)->where('status','pending')->firstOrFail();
        $match->update(['status' => 'declined', 'declined_at' => now()]);
        return back()->with('success', __('match.declined'));
    }
}
