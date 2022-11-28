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
        Schema::create('secteurs_sous_secteurs', function (Blueprint $table) {
            $table->unsignedInteger('secteur_id');
            $table->unsignedInteger('sous_secteur_id');
            $table->primary(['secteur_id','sous_secteur_id'],'secteur_sous_secteur_id');
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
        Schema::dropIfExists('secteurs_sous_secteurs');
    }
};
