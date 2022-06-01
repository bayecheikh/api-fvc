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
        Schema::create('mode_financements_investissements', function (Blueprint $table) {
            $table->unsignedInteger('mode_financement_id');
            $table->unsignedInteger('investissement_id');
            $table->primary(['mode_financement_id','investissement_id'],'mode_investissement_id');
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
        Schema::dropIfExists('mode_financements_investissements');
    }
};
