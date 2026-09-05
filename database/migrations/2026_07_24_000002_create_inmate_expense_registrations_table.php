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
        if (Schema::hasTable('inmate_expense_registrations')) {
            Schema::table('inmate_expense_registrations', function (Blueprint $table): void {
                $table->unique('inmate_intake_registration_id', 'expense_inmate_unique');
                $table->foreign('inmate_intake_registration_id', 'expense_inmate_fk')
                    ->references('id')
                    ->on('inmate_intake_registrations')
                    ->cascadeOnDelete();
            });

            return;
        }

        Schema::create('inmate_expense_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('inmate_intake_registration_id');
            $table->date('certificate_date')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('full_name');
            $table->string('gender')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country_of_birth')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('sentencing_court')->nullable();
            $table->string('court_file_number')->nullable();
            $table->string('crime_type')->nullable();
            $table->string('crime_identification_number')->nullable();
            $table->string('institution_id_number')->nullable();
            $table->text('education_skill_before')->nullable();
            $table->string('previous_profession')->nullable();
            $table->string('education_period_provided')->nullable();
            $table->text('work_training_provided')->nullable();
            $table->text('work_experience_during')->nullable();
            $table->string('work_type_assigned')->nullable();
            $table->text('release_reason')->nullable();
            $table->date('release_date')->nullable();
            $table->text('health_condition')->nullable();
            $table->string('official_name')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();

            $table->unique('inmate_intake_registration_id', 'expense_inmate_unique');
            $table->foreign('inmate_intake_registration_id', 'expense_inmate_fk')
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
        Schema::dropIfExists('inmate_expense_registrations');
    }
};
