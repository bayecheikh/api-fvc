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
        Schema::create('monnaies_investissements', function (Blueprint $table) {
            $table->unsignedInteger('monnaie_id');
            $table->unsignedInteger('investissement_id');
            $table->primary(['monnaie_id','investissement_id']);
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
        Schema::dropIfExists('monnaies_investissements');
    }
};
