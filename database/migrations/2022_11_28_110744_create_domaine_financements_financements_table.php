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
        Schema::create('domaine_fines_fines', function (Blueprint $table) {
            $table->unsignedInteger('domaine_financement_id');
            $table->unsignedInteger('financement_id');
            $table->primary(['domaine_financement_id','financement_id'],'domaine_financement_fine_id');
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
        Schema::dropIfExists('domaine_financements_financements');
    }
};
