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

        Schema::create('sample_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->constrained();
            $table->foreignId('test_id')->constrained();
            $table->morphs('referralable', 'laboratories'); // lab to which the sample is referred
            $table->foreignId('reason_id')->nullable()->constrained('sample_referral_reasons');
            $table->string('referral_code')->nullable();
            $table->string('referral_type')->default('External'); // External or Internal
            $table->string('courier')->nullable();
            $table->string('storage_condition')->nullable();
            $table->string('transport_medium')->nullable();
            $table->string('sample_integrity')->nullable();
            $table->decimal('temperature_on_dispatch', 5, 2)->nullable();
            $table->text('additional_notes')->nullable();
            $table->dateTime('date_referred');
            $table->text('reason')->nullable();
            $table->string('status')->default('Pending'); // pending, received
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('creator_lab')->constrained('laboratories');
            $table->timestamps();
        });

        // Migration for test results
        Schema::table('test_results', function (Blueprint $table) {
            $table->foreignId('referral_id')->nullable();
            $table->string('ref_result_file')->nullable();
            $table->text('ref_comments')->nullable();
            $table->date('received_date')->nullable();
            $table->foreignId('received_by')->constrained('users')->nullable();
        });

// Add to samples table
        Schema::table('samples', function (Blueprint $table) {
            $table->text('referred_tests')->after('tests_performed')->nullable();;
        });
        Schema::table('test_assignments', function (Blueprint $table) {
            $table->boolean('is_referred')->after('tests_performed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_referrals');
    }
};
