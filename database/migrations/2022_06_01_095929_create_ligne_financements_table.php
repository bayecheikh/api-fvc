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
        Schema::create('ligne_financements', function (Blueprint $table) {
            $table->id();
            $table->string('id_pilier')->nullable();
            $table->string('id_axe')->nullable();
            $table->string('montantBienServicePrevus')->nullable();
            $table->string('montantBienServiceMobilises')->nullable();
            $table->string('montantBienServiceExecutes')->nullable();
            $table->string('montantInvestissementPrevus')->nullable();
            $table->string('montantInvestissementMobilises')->nullable();
            $table->string('montantInvestissementExecutes')->nullable();
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
        Schema::dropIfExists('ligne_financements');
    }
};
