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
        Schema::create('ligne_financements_investissements', function (Blueprint $table) {
            $table->unsignedInteger('ligne_financement_id');
            $table->unsignedInteger('investissement_id');
            $table->primary(['ligne_financement_id','investissement_id'],'ligne_investissemnt_id');
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
        Schema::dropIfExists('ligne_financements_investissements');
    }
};
