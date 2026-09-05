<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inmate_file_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inmate_file_record_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('page_number');
            $table->string('image_path');
            $table->timestamps();

            $table->unique(['inmate_file_record_id', 'page_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inmate_file_pages');
    }
};
