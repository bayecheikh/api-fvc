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
        Schema::create('source_type_sources', function (Blueprint $table) {
            $table->unsignedInteger('source_id');
            $table->unsignedInteger('type_sources_id');
            $table->primary(['source_id','type_sources_id']);
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
        Schema::dropIfExists('source_financement_type_sources');
    }
};
