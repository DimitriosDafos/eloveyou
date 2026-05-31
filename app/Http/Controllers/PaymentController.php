<?php
namespace App\Http\Controllers;
use App\Models\Chat;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PaymentController extends Controller
{
    public function showChatPayment(int $id)
    {
        $chat = Chat::findOrFail($id);
        $user = auth()->user();
        $isSubscribed = $user->isSubscribed();
        $amount = $isSubscribed ? 0.99 : 3.99;
        return view('payment.chat', compact('chat', 'isSubscribed', 'amount'));
    }

    public function stripeChat(Request $request, int $id)
    {
        $chat = Chat::findOrFail($id);
        $user = auth()->user();
        $isExtension = $chat->expires_at && $chat->expires_at->isPast();
        $amount = ($user->isSubscribed() || $isExtension) ? 99 : 399;
        $label = $isExtension ? __('payment.extend_24h') : __('payment.unlock_chat');

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $amount,
                    'product_data' => ['name' => $label],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?chat=' . $chat->id . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('payment.cancel'),
            'metadata'    => ['chat_id' => $chat->id, 'user_id' => $user->id, 'type' => $isExtension ? 'chat_extension' : 'chat_24h'],
        ]);

        return redirect($session->url);
    }

    public function showSubscribe()
    {
        return view('payment.subscribe');
    }

    public function stripeSubscribe(Request $request)
    {
        $plan = $request->validate(['plan' => 'required|in:monthly,yearly'])['plan'];
        $amount = $plan === 'monthly' ? 1499 : 9900;
        $label = $plan === 'monthly' ? __('payment.monthly_plan') : __('payment.yearly_plan');

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $amount,
                    'product_data' => ['name' => $label],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?plan=' . $plan . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('payment.cancel'),
            'metadata'    => ['user_id' => auth()->id(), 'plan' => $plan, 'type' => $plan],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $user = auth()->user();
        if ($request->has('plan')) {
            $plan = $request->plan;
            Subscription::create([
                'user_id'    => $user->id,
                'plan'       => $plan,
                'chat_limit' => $plan === 'monthly' ? 10 : null,
                'status'     => 'active',
                'provider'   => 'stripe',
                'started_at' => now(),
                'expires_at' => $plan === 'monthly' ? now()->addMonth() : now()->addYear(),
            ]);
        } elseif ($request->has('chat')) {
            $chat = Chat::findOrFail($request->chat);
            $chat->update(['expires_at' => now()->addHours(24), 'photos_revealed' => true]);
        }
        return redirect()->route('chats.index')->with('success', __('payment.success'));
    }

    public function cancel()
    {
        return redirect()->back()->with('warning', __('payment.cancelled'));
    }

    public function stripeWebhook(Request $request)
    {
        return response()->json(['status' => 'ok']);
    }

    public function paypalChat(Request $request, int $id) { return redirect()->route('payment.chat', $id)->with('info', __('payment.paypal_soon')); }
    public function paypalSubscribe(Request $request) { return redirect()->route('payment.subscribe')->with('info', __('payment.paypal_soon')); }
}
