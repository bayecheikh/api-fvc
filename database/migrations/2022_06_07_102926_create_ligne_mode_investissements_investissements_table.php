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
        Schema::create('ligne_mode_investissements_investissements', function (Blueprint $table) {
            $table->unsignedInteger('ligne_mode_investissement_id');
            $table->unsignedInteger('investissement_id');
            $table->primary(['ligne_mode_investissement_id','investissement_id'],'ligne_mode_investissement');
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
        Schema::dropIfExists('ligne_mode_investissements_investissements');
    }
};
