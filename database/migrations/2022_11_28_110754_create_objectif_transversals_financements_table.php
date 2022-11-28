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
        Schema::create('objectif_transversals_fines', function (Blueprint $table) {
            $table->unsignedInteger('objectif_transversal_id');
            $table->unsignedInteger('financement_id');
            $table->primary(['objectif_transversal_id','financement_id'],'objectif_transversal_fine_id');
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
        Schema::dropIfExists('objectif_transversals_financements');
    }
};
