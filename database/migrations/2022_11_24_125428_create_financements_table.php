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
        Schema::create('financements', function (Blueprint $table) {
            $table->id();
            $table->string('date_debut')->nullable();
            $table->string('date_fin')->nullable();
            $table->string('titre_projet')->nullable();
            $table->string('objectif_global_projet')->nullable();
            $table->string('montant_total_adaptation')->nullable();
            $table->string('montant_total_attenuation')->nullable();
            $table->string('montant_total_execute')->nullable();
            $table->string('montant_total_restant')->nullable();
            $table->string('status')->nullable();
            $table->string('state')->nullable();
            $table->string('motif_rejet')->nullable(); 
            $table->string('brouillon')->nullable();
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
        Schema::dropIfExists('financements');
    }
};
