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
        Schema::create('test_result_amendments', function (Blueprint $table) {
            $table->id();   
            $table->foreignId('test_result_id')->constrained('test_results','id')->onDelete('Restrict');
            $table->string('amendment_type')->nullable();
            $table->text('original_results')->nullable();
            $table->text('amendment_comment')->nullable();
            $table->unsignedBigInteger('amended_by')->nullable();
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
        Schema::dropIfExists('test_result_amendments');
    }
};
