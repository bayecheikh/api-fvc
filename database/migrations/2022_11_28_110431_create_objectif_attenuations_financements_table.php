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
        Schema::create('objectif_attenuations_fines', function (Blueprint $table) {
            $table->unsignedInteger('objectif_attenuation_id');
            $table->unsignedInteger('financement_id');
            $table->primary(['objectif_attenuation_id','financement_id'],'objectif_attenuation_fine_id');
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
        Schema::dropIfExists('objectif_attenuations_financements');
    }
};
