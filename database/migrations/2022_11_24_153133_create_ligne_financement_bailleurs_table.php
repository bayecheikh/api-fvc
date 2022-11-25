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
        Schema::create('ligne_financement_bailleurs', function (Blueprint $table) {
            $table->id();
            $table->string('id_investissement')->nullable();
            $table->string('id_bailleur')->nullable();
            $table->string('id_instrument_financier')->nullable();
            $table->string('montant_total')->nullable();
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
        Schema::dropIfExists('ligne_financement_bailleurs');
    }
};
