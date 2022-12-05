<?php

use App\Models\FacilityInformation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Facades\CauserResolver;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_information', function (Blueprint $table) {
            $table->id();
            $table->string('facility_name');
            $table->string('slogan');
            $table->text('about');
            $table->string('facility_type');
            $table->string('physical_address');
            $table->string('address2')->nullable();
            $table->string('contact');
            $table->string('contact2')->nullable();
            $table->string('email')->nullable();
            $table->string('email2')->nullable();
            $table->string('tin')->nullable();
            $table->string('website')->nullable();
            $table->string('fax')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo2')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // CauserResolver::setCauser(1);
        $facilityInfo = new FacilityInformation();
        $facilityInfo->disableLogging();
        $facilityInfo->facility_name = 'Makerere Biomedical Research Centre';
        $facilityInfo->slogan = 'For Effiecency and Productivity';
        $facilityInfo->about = 'Best in Lab practices';
        $facilityInfo->facility_type = 'Health';
        $facilityInfo->physical_address = '245';
        $facilityInfo->address2 = 'Kamapala Uganda';
        $facilityInfo->contact = '+256 45757575';
        $facilityInfo->contact2 = '+256 7055464';
        $facilityInfo->email = 'support@autolab.co.ug';
        $facilityInfo->email2 = 'info@autolab.co.ug';
        $facilityInfo->fax = '+25645363633';
        $facilityInfo->website = 'www.autolab.co.ug';
        $facilityInfo->tin = '101388383';
        $facilityInfo->logo = 'logo.png';
        $facilityInfo->logo2 = 'logo.png';
        $facilityInfo->created_by = 1;
        $facilityInfo->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facility_information');
    }
};
