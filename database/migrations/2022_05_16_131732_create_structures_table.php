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
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom_structure');
            $table->string('numero_autorisation');
            $table->string('accord_siege');
            $table->string('numero_agrement');
            $table->string('adresse_structure');
            $table->string('debut_intervention');
            $table->string('fin_intervention');
            $table->string('telephone_structure');
            $table->string('email_structure');

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
        Schema::dropIfExists('structures');
    }
};
