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
        Schema::create('agence_acredites_fines', function (Blueprint $table) {
            $table->unsignedInteger('agence_acredite_id');
            $table->unsignedInteger('financement_id');
            $table->primary(['agence_acredite_id','financement_id'],'agence_acredite_fine_id');
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
        Schema::dropIfExists('agence_acredites_financements');
    }
};
