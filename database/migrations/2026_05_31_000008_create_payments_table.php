<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chat_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['chat_24h', 'chat_extension', 'monthly', 'yearly']);
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('provider', ['stripe', 'paypal']);
            $table->string('provider_payment_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
