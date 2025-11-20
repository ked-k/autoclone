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
        Schema::table('test_results', function (Blueprint $table) {
            $table->string('amended_state')->default(false)->after('kit_expiry_date');
            $table->string('amendment_type')->nullable()->after('amended_state');
            $table->text('original_results')->nullable()->after('amendment_type');
            $table->text('amendment_comment')->nullable()->after('original_results');
            $table->unsignedBigInteger('amended_by')->nullable()->after('amendment_comment');
            $table->dateTime('amended_at')->nullable()->after('amended_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_results');
    }
};
