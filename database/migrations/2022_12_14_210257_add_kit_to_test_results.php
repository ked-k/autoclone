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
            $table->text('parameters')->nullable();
            $table->foreignId('kit_id')->nullable();
            $table->string('verified_lot')->nullable();
            $table->date('kit_expiry_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn(['kit_id', 'verified_lot', 'kit_expiry_date']);
        });
    }
};
