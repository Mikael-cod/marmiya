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
        Schema::create('inmate_file_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('inmate_intake_registration_id');
            $table->date('birth_date')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('birth_region', 100)->nullable();
            $table->string('birth_zone', 100)->nullable();
            $table->string('birth_woreda', 100)->nullable();
            $table->string('birth_kebele', 100)->nullable();
            $table->string('residence_region', 100)->nullable();
            $table->string('residence_zone', 100)->nullable();
            $table->string('residence_woreda', 100)->nullable();
            $table->string('residence_kebele', 100)->nullable();
            $table->string('education_level', 100)->nullable();
            $table->string('occupation', 255)->nullable();
            $table->string('ethnicity', 100)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('marital_status', 100)->nullable();
            $table->string('height', 50)->nullable();
            $table->string('hair_type', 100)->nullable();
            $table->string('appearance', 100)->nullable();
            $table->string('forehead', 100)->nullable();
            $table->string('nose', 100)->nullable();
            $table->string('eye_color', 100)->nullable();
            $table->string('teeth', 100)->nullable();
            $table->string('lips', 100)->nullable();
            $table->string('ears', 100)->nullable();
            $table->string('distinguishing_mark', 255)->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_region', 100)->nullable();
            $table->string('emergency_zone', 100)->nullable();
            $table->string('emergency_woreda', 100)->nullable();
            $table->string('emergency_kebele', 100)->nullable();
            $table->string('emergency_phone_landline', 50)->nullable();
            $table->string('emergency_phone_mobile', 50)->nullable();
            $table->string('filled_by_professional_name', 255)->nullable();
            $table->string('signature', 255)->nullable();
            $table->timestamps();

            $table->unique('inmate_intake_registration_id', 'inmate_file_inmate_unique');
            $table->foreign('inmate_intake_registration_id', 'inmate_file_inmate_fk')
                ->references('id')
                ->on('inmate_intake_registrations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmate_file_records');
    }
};
