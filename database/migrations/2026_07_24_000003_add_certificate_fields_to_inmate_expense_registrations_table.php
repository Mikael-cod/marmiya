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
        Schema::table('inmate_expense_registrations', function (Blueprint $table): void {
            if (! Schema::hasColumn('inmate_expense_registrations', 'religion')) {
                $table->string('religion')->nullable()->after('age');
            }

            if (! Schema::hasColumn('inmate_expense_registrations', 'sentence_duration')) {
                $table->string('sentence_duration')->nullable()->after('sentencing_court');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inmate_expense_registrations', function (Blueprint $table): void {
            if (Schema::hasColumn('inmate_expense_registrations', 'religion')) {
                $table->dropColumn('religion');
            }

            if (Schema::hasColumn('inmate_expense_registrations', 'sentence_duration')) {
                $table->dropColumn('sentence_duration');
            }
        });
    }
};
