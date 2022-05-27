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
        Schema::table('structures', function (Blueprint $table) {
            $table->string('numero_autorisation');
            $table->string('accord_siege');
            $table->string('numero_agrement');
            $table->string('adresse_structure');
            $table->string('debut_intervention');
            $table->string('fin_intervention');
            $table->string('telephone_structure');
            $table->string('email_structure');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('structures', function (Blueprint $table) {
            $table->dropColumn('numero_autorisation');
            $table->dropColumn('accord_siege');
            $table->dropColumn('numero_agrement');
            $table->dropColumn('adresse_structure');
            $table->dropColumn('debut_intervention');
            $table->dropColumn('fin_intervention');
            $table->dropColumn('telephone_structure');
            $table->dropColumn('email_structure');
        });
    }
};
