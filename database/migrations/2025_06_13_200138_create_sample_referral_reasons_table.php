<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sample_referral_reasons');
        Schema::create('sample_referral_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('creator_lab')->nullable();
            $table->timestamps();
        });
        DB::table('sample_referral_reasons')->insert([
            ['name' => 'Equipment break down'],
            ['name' => 'Reagent stock out'],
            ['name' => 'Supplies stock out'],
            ['name' => 'Power outage'],
            ['name' => 'No testing expertise'],
            ['name' => 'Lack of required equipment'],
            ['name' => 'Confirmatory testing'],
            ['name' => 'For QA re-testing'],
            ['name' => 'Referral policy requirement'],
            ['name' => 'Sample complexity'],
            ['name' => 'Test not available onsite'],
            ['name' => 'Temporary site closure'],
            ['name' => 'Workforce shortage'],
            ['name' => 'Preventive maintenance'],
            ['name' => 'Calibration pending'],
            ['name' => 'Inadequate biosafety conditions'],
            ['name' => 'Cold chain failure'],
            ['name' => 'Client request'],
            ['name' => 'Regulatory requirement'],
            ['name' => 'Others'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_referral_reasons');
    }
};
