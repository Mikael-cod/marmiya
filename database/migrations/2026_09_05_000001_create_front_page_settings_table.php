<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('front_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->string('institute');
            $table->string('subtitle');
            $table->text('login_description');
            $table->string('secure_platform');
            $table->string('welcome_back');
            $table->string('enter_credentials');
            $table->string('contact_support');
            $table->string('contact_support_url')->nullable();
            $table->string('contact_administrator_url')->nullable();
            $table->string('help_center_url')->nullable();
            $table->string('copyright');
            $table->boolean('show_secure_badge')->default(true);
            $table->string('default_theme')->default('light');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('front_page_settings');
    }
};
