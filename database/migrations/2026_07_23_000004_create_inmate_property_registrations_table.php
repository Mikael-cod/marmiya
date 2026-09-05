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
        Schema::create('inmate_property_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('inmate_intake_registration_id');
            $table->decimal('entry_cash_amount', 12, 2)->nullable();
            $table->string('form_85_number', 100)->nullable();
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->string('form_86_number', 100)->nullable();
            $table->decimal('withdrawal_amount', 12, 2)->nullable();
            $table->string('other_property_receipt_number', 100)->nullable();
            $table->timestamps();

            $table->foreign('inmate_intake_registration_id', 'inmate_property_inmate_fk')
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
        Schema::dropIfExists('inmate_property_registrations');
    }
};
