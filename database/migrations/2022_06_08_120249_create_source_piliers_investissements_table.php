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
        Schema::create('piliers_investissements', function (Blueprint $table) {
            $table->unsignedInteger('pilier_id');
            $table->unsignedInteger('investissement_id');
            $table->primary(['pilier_id','investissement_id']);
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
        Schema::dropIfExists('piliers_investissements');
    }
};
