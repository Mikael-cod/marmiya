<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('system_settings');
    }

    public function down(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('zone')->nullable();
            $table->string('woreda')->nullable();
            $table->string('city')->nullable();
            $table->string('address_line')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('official_email')->nullable();
            $table->string('director_name')->nullable();
            $table->string('registrar_office')->nullable();
            $table->unsignedSmallInteger('default_per_page')->default(15);
            $table->unsignedSmallInteger('default_report_eth_year')->nullable();
            $table->unsignedTinyInteger('default_report_eth_month')->nullable();
            $table->string('report_header_title')->nullable();
            $table->string('report_header_subtitle')->nullable();
            $table->text('report_footer_text')->nullable();
            $table->text('system_notice')->nullable();
            $table->boolean('show_system_notice')->default(false);
            $table->text('operations_guidelines')->nullable();
            $table->timestamps();
        });
    }
};
