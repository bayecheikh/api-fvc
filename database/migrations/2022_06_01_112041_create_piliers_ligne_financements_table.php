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
        Schema::create('piliers_ligne_financements', function (Blueprint $table) {
            $table->unsignedInteger('pilier_id');
            $table->unsignedInteger('ligne_financement_id');
            $table->primary(['pilier_id','ligne_financement_id'],'pilier_ligne_id');
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
        Schema::dropIfExists('piliers_ligne_financements');
    }
};
