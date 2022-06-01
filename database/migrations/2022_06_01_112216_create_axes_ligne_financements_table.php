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
        Schema::create('axes_ligne_financements', function (Blueprint $table) {
            $table->unsignedInteger('axe_id');
            $table->unsignedInteger('ligne_financement_id');
            $table->primary(['axe_id','ligne_financement_id']);
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
        Schema::dropIfExists('axes_ligne_financements');
    }
};
