<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('acceptor_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('photos_revealed')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('chats'); }
};
