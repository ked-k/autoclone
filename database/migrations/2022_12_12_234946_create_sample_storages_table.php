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
        Schema::create('sample_storages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('freezer_id');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('rack_id')->nullable();
            $table->unsignedBigInteger('drawer_id')->nullable();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->string('box_row')->nullable();
            $table->string('box_column')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('creator_lab');
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
        Schema::dropIfExists('sample_storages');
    }
};
