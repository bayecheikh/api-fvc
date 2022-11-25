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
        Schema::create('ligne_financement_secteurs', function (Blueprint $table) {
            $table->id();
            $table->string('id_investissement')->nullable();
            $table->string('id_secteur')->nullable();
            $table->string('id_sous_secteur')->nullable();
            $table->string('montant_adaptation')->nullable();
            $table->string('montant_attenuation')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('ligne_financement_secteurs');
    }
};
