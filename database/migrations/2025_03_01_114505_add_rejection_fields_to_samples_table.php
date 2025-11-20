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
        Schema::table('samples', function (Blueprint $table) {
            $table->unsignedBigInteger('commented_by')->nullable()->after('status');
            $table->text('sample_comment')->nullable()->after('status');
            $table->dateTime('commented_at')->nullable()->after('status');
            $table->text('rejection_reason')->nullable()->after('status');
        });

        Schema::table('test_results', function (Blueprint $table) {
            $table->text('tat_comment')->nullable()->after('amended_at');
        });
        Schema::table('tests', function (Blueprint $table) {
            $table->boolean('accreditation')->default(false)->after('status');
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
