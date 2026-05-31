<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->text('real_name_encrypted');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('phone_verification_code', 10)->nullable();
            $table->timestamp('phone_code_expires_at')->nullable();
            $table->integer('age');
            $table->string('gender');
            $table->json('looking_for');
            $table->string('location_city');
            $table->string('location_region');
            $table->text('bio')->nullable();
            $table->string('locale', 5)->default('en');
            $table->boolean('is_incognito')->default(false);
            $table->boolean('profile_complete')->default(false);
            $table->boolean('is_suspended')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->string('admin_role')->nullable();
            $table->boolean('age_confirmed')->default(false);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
