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
        Schema::create('sample_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->text('possible_tests')->nullable();
            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->references('id')->on('users')->nullable()->constrained()->onUpdate('cascade')->onDelete('restrict');
            $table->unsignedBigInteger('creator_lab')->nullable();
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
        Schema::dropIfExists('sample_types');
    }
};
