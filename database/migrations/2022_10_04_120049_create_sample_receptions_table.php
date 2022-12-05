<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('sample_receptions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->dateTime('date_delivered');
            $table->integer('samples_delivered');
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('courier_id');
            $table->integer('samples_accepted');
            $table->integer('samples_rejected')->nullable();
            $table->integer('samples_handled')->default(0);
            $table->unsignedBigInteger('received_by')->nullable();
            $table->boolean('courier_signed')->default(0);
            $table->unsignedBigInteger('creator_lab')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('date_reviewed')->nullable();
            $table->longText('comment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('status')->default('Reviewed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_receptions');
    }
};
