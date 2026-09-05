<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('password_min_length')->default(8);
            $table->boolean('password_require_letters')->default(true);
            $table->boolean('password_require_mixed_case')->default(false);
            $table->boolean('password_require_numbers')->default(true);
            $table->boolean('password_require_symbols')->default(false);
            $table->unsignedSmallInteger('login_max_attempts')->default(5);
            $table->unsignedSmallInteger('login_lockout_minutes')->default(5);
            $table->unsignedSmallInteger('session_lifetime_minutes')->default(120);
            $table->boolean('expire_session_on_close')->default(false);
            $table->boolean('force_https')->default(false);
            $table->string('security_contact_email')->nullable();
            $table->text('security_guidelines')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
