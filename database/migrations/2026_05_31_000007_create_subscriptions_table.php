<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('plan', ['monthly', 'yearly']);
            $table->integer('chat_limit')->nullable();
            $table->integer('chats_used')->default(0);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->string('provider')->nullable();
            $table->string('provider_subscription_id')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subscriptions'); }
};
