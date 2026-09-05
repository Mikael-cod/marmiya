<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inmate_intake_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('court_file_number')->nullable();
            $table->string('institution_file_number')->nullable();
            $table->string('cell_number')->nullable();
            $table->string('full_name');
            $table->string('crime_type')->nullable();
            $table->string('detaining_court')->nullable();
            $table->date('admission_date')->nullable();
            $table->time('admission_time')->nullable();
            $table->string('verdict_court')->nullable();
            $table->string('sentence_status')->nullable();
            $table->string('sentence_duration')->nullable();
            $table->date('verdict_date')->nullable();
            $table->string('appeal_court')->nullable();
            $table->date('sentence_start_date')->nullable();
            $table->date('sentence_end_date')->nullable();
            $table->date('parole_release_date')->nullable();
            $table->text('release_reason')->nullable();
            $table->date('full_release_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmate_intake_registrations');
    }
};
