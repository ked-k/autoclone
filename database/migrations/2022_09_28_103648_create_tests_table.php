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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->references('id')->on('test_categories')->nullable()->constrained()->onUpdate('cascade')->onDelete('restrict');
            $table->string('name');
            $table->string('short_code')->nullable()->unique();
            $table->double('price', 8, 2)->default(0);
            $table->integer('tat')->default(0);
            $table->string('reference_range_min')->nullable();
            $table->string('reference_range_max')->nullable();
            $table->longText('precautions')->nullable();
            $table->string('result_type')->nullable();
            $table->text('absolute_results')->nullable();
            $table->string('measurable_result_uom')->nullable();
            $table->text('comments')->nullable();
            $table->text('parameters')->nullable();
            $table->string('parameter_uom')->nullable();
            $table->string('result_presentation');
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('tests');
    }
};
