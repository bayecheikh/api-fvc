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
            $table->string('numero_autorisation')->nullable();
            $table->string('accord_siege')->nullable();
            $table->string('numero_agrement')->nullable();
            $table->string('adresse_structure')->nullable();
            $table->string('debut_intervention')->nullable();
            $table->string('fin_intervention')->nullable();
            $table->string('telephone_structure')->nullable();
            $table->string('email_structure')->nullable();
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
