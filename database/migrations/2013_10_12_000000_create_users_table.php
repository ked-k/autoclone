<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
        // Create table for storing departments/labs
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->string('laboratory_name', 50)->unique();
            $table->string('description')->nullable();
            $table->string('short_code', 6)->nullable()->unique();
            $table->integer('autonumber')->nullable();
            $table->text('associated_facilities')->nullable();
            $table->text('associated_studies')->nullable();
            $table->integer('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('creator_lab')->nullable();
            $table->timestamps();
        });

        // Create table for storing designations/positions
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->integer('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Create table for storing users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 14)->nullable()->unique();
            $table->string('surname', 25);
            $table->string('first_name', 25);
            $table->string('other_name', 25)->nullable();
            $table->string('name', 25);

            $table->unsignedBigInteger('laboratory_id')->nullable();
            $table->foreign('laboratory_id')->references('id')->on('laboratories')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('designation_id')->nullable();
            $table->foreign('designation_id')->references('id')->on('designations')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->string('email', 30)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('password_updated_at')->default(now());
            $table->string('contact', 20)->nullable();
            $table->string('title', 6)->nullable();
            $table->string('avatar')->nullable();
            $table->string('signature')->nullable();
            $table->integer('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('color_scheme')->nullable();
            $table->unsignedBigInteger('creator_lab')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Seed admin user
        // Artisan::call('db:seed', [
        //     '--class' => 'UserSeeder',
        //     '--force' => true, // <--- add this line
        // ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('laboratories');
        Schema::dropIfExists('designations');
    }
};
