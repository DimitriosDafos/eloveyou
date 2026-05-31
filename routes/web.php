<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Admin\AdminController;

// Locale switch
Route::post('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/',           [LoginController::class, 'show'])->name('login');
    Route::post('/login',     [LoginController::class, 'login'])->name('login.post');
    Route::get('/register',   [RegisterController::class, 'showStep1'])->name('register');
    Route::post('/register',  [RegisterController::class, 'postStep1'])->name('register.post');
    Route::get('/verify',     [RegisterController::class, 'showVerify'])->name('register.verify');
    Route::post('/verify',    [RegisterController::class, 'postVerify'])->name('register.verify.post');
    Route::post('/verify/resend', [RegisterController::class, 'resendCode'])->name('register.resend');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Profile setup (no profile_complete check here — it's the setup itself)
    Route::get('/profile/setup',         [ProfileController::class, 'setup'])->name('profile.setup');
    Route::post('/profile/setup',        [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/photos',        [ProfileController::class, 'photos'])->name('profile.photos');
    Route::post('/profile/photos',       [ProfileController::class, 'uploadPhoto'])->name('profile.photos.upload');
    Route::delete('/profile/photos/{id}',[ProfileController::class, 'deletePhoto'])->name('profile.photos.delete');
    Route::post('/profile/incognito',    [ProfileController::class, 'toggleIncognito'])->name('profile.incognito');
    Route::delete('/profile',            [ProfileController::class, 'destroy'])->name('profile.delete');

    // Locale
    Route::post('/settings/locale', [LocaleController::class, 'save'])->name('locale.save');

    // Requires complete profile
    Route::middleware(['profile.complete'])->group(function () {
        Route::get('/browse',               [BrowseController::class, 'index'])->name('browse.index');
        Route::get('/profile/{username}',   [BrowseController::class, 'show'])->name('profile.show');
        Route::post('/match/{userId}',      [MatchController::class, 'send'])->name('match.send');
        Route::post('/match/{id}/accept',   [MatchController::class, 'accept'])->name('match.accept');
        Route::post('/match/{id}/decline',  [MatchController::class, 'decline'])->name('match.decline');
        Route::get('/matches',              [MatchController::class, 'index'])->name('matches.index');
        Route::get('/chat/{id}',            [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{id}/message',   [ChatController::class, 'sendMessage'])->name('chat.message');
        Route::post('/chat/{id}/extend',    [ChatController::class, 'extend'])->name('chat.extend');
        Route::get('/chats',                [ChatController::class, 'index'])->name('chats.index');
        Route::post('/block/{userId}',      [BrowseController::class, 'block'])->name('user.block');
        Route::post('/report/{userId}',     [BrowseController::class, 'report'])->name('user.report');

        // Payments
        Route::get('/payment/chat/{id}',          [PaymentController::class, 'showChatPayment'])->name('payment.chat');
        Route::post('/payment/chat/{id}/stripe',   [PaymentController::class, 'stripeChat'])->name('payment.chat.stripe');
        Route::post('/payment/chat/{id}/paypal',   [PaymentController::class, 'paypalChat'])->name('payment.chat.paypal');
        Route::get('/payment/subscribe',           [PaymentController::class, 'showSubscribe'])->name('payment.subscribe');
        Route::post('/payment/subscribe/stripe',   [PaymentController::class, 'stripeSubscribe'])->name('payment.subscribe.stripe');
        Route::post('/payment/subscribe/paypal',   [PaymentController::class, 'paypalSubscribe'])->name('payment.subscribe.paypal');
        Route::get('/payment/success',             [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/cancel',              [PaymentController::class, 'cancel'])->name('payment.cancel');
    });
});

// Admin panel
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                      [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',                 [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}',            [AdminController::class, 'userDetail'])->name('users.detail');
    Route::post('/users/{id}/ban',       [AdminController::class, 'ban'])->name('users.ban');
    Route::post('/users/{id}/suspend',   [AdminController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{id}/restore',   [AdminController::class, 'restore'])->name('users.restore');
    Route::get('/reports',               [AdminController::class, 'reports'])->name('reports');
    Route::post('/reports/{id}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');
    Route::get('/photos',                [AdminController::class, 'photos'])->name('photos');
    Route::post('/photos/{id}/approve',  [AdminController::class, 'approvePhoto'])->name('photos.approve');
    Route::post('/photos/{id}/remove',   [AdminController::class, 'removePhoto'])->name('photos.remove');
    Route::get('/payments',              [AdminController::class, 'payments'])->name('payments');
    Route::get('/stats',                 [AdminController::class, 'stats'])->name('stats');
    Route::get('/admins',                [AdminController::class, 'admins'])->name('admins');
    Route::post('/admins',               [AdminController::class, 'addAdmin'])->name('admins.add');
    Route::delete('/admins/{id}',        [AdminController::class, 'removeAdmin'])->name('admins.remove');
});

// Webhook
Route::post('/webhooks/stripe', [PaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');
