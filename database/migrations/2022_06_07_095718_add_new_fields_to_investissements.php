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
        Schema::table('investissements', function (Blueprint $table) {
            $table->string('state')->nullable();
            $table->longText('motif_rejet')->nullable();
            $table->string('brouillon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investissements', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->dropColumn('motif_rejet');
            $table->dropColumn('brouillon');
        });
    }
};
