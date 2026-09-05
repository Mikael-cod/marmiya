<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inmate_intake_registrations', function (Blueprint $table): void {
            $table->unsignedBigInteger('mother_inmate_intake_registration_id')->nullable()->after('user_id');
            $table->foreign('mother_inmate_intake_registration_id', 'intake_mother_fk')
                ->references('id')
                ->on('inmate_intake_registrations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inmate_intake_registrations', function (Blueprint $table): void {
            $table->dropForeign('intake_mother_fk');
            $table->dropColumn('mother_inmate_intake_registration_id');
        });
    }
};
