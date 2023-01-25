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
        Schema::table('financements', function (Blueprint $table) {
            $table->string('renforcement_capacite')->nullable();
            $table->string('transfert_technologie')->nullable();
            $table->string('montant_total')->nullable();
            $table->string('nombre_beneficiaire')->nullable();
            $table->string('volume_co2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financements', function (Blueprint $table) {
            //
        });
    }
};
