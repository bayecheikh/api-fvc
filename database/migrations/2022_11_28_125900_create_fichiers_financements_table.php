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
        Schema::create('fichiers_fines', function (Blueprint $table) {
            $table->unsignedInteger('fichier_id');
            $table->unsignedInteger('financement_id');
            $table->primary(['fichier_id','financement_id'],'fichier_fine_id');
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
        Schema::dropIfExists('fichiers_financements');
    }
};
