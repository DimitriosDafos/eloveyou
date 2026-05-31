<?php
namespace App\Http\Controllers;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $chats = Chat::where('requester_id', $userId)->orWhere('acceptor_id', $userId)
            ->with(['requester','acceptor','messages' => fn($q) => $q->latest()->limit(1)])
            ->latest()->get();
        return view('chat.index', compact('chats'));
    }

    public function show(int $id)
    {
        $userId = auth()->id();
        $chat = Chat::where(fn($q) => $q->where('requester_id',$userId)->orWhere('acceptor_id',$userId))
            ->with(['messages.sender','requester.photos','acceptor.photos'])
            ->findOrFail($id);
        $chat->messages()->where('sender_id','!=',$userId)->update(['read'=>true,'read_at'=>now()]);
        return view('chat.show', compact('chat'));
    }

    public function sendMessage(Request $request, int $id)
    {
        $request->validate(['body' => 'required|string|max:1000']);
        $userId = auth()->id();
        $chat = Chat::where(fn($q) => $q->where('requester_id',$userId)->orWhere('acceptor_id',$userId))->findOrFail($id);

        if ($chat->isExpired()) {
            return back()->withErrors(['body' => __('chat.expired')]);
        }

        $message = $chat->messages()->create([
            'sender_id' => $userId,
            'body'      => $request->body,
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();
        return back();
    }

    public function extend(Request $request, int $id)
    {
        $chat = Chat::findOrFail($id);
        return redirect()->route('payment.chat', $chat->id);
    }
}
