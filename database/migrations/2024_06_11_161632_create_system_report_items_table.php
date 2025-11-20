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
        Schema::create('system_report_items', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('system_report_id')->constrained('system_reports', 'id')->onUpdate('cascade')->onDelete('restrict');
            $table->string('module',180);
            $table->string('result',180);
            $table->float('score');
            $table->enum('status',['Pending','Submitted','Reviewed','Approved'])->default('Pending');
            $table->longText('details')->nullable();
            $table->text('reviewer_comment')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users', 'id')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('system_report_items');
    }
};
