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
        Schema::create('system_reports', function (Blueprint $table) {
            $table->id();
            $table->string('ref_code',20)->unique();
            $table->enum('status',['Pending','Submitted','Reviewed','Approved'])->default('Pending');
            $table->text('comments')->nullable();
            $table->date('report_date');
            $table->date('submitted_at')->nullable();
            $table->text('reviewer_comment')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->unsignedBigInteger('creator_lab')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users', 'id')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('created_by')->nullable()->constrained('users', 'id')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('system_reports');
    }
};
