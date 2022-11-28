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
        Schema::create('ligne_fine_zones_fines', function (Blueprint $table) {
            $table->unsignedInteger('ligne_financement_zone_id');
            $table->unsignedInteger('financement_id');
            $table->primary(['ligne_financement_zone_id','financement_id'],'ligne_fine_zone_fine_id');
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
        Schema::dropIfExists('ligne_financement_zones_financements');
    }
};
