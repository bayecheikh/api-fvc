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
        Schema::create('source_fines_fines', function (Blueprint $table) {
            $table->unsignedInteger('source_financement_id');
            $table->unsignedInteger('financement_id');
            $table->primary(['source_financement_id','financement_id'],'source_financement_fine_id');
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
        Schema::dropIfExists('source_financements_financements');
    }
};
