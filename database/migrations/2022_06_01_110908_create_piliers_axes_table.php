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
        Schema::create('piliers_axes', function (Blueprint $table) {
            $table->unsignedInteger('pilier_id');
            $table->unsignedInteger('axe_id');
            $table->primary(['pilier_id','axe_id']);
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
        Schema::dropIfExists('piliers_axes');
    }
};
