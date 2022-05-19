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
        Schema::create('structures_type_sources', function (Blueprint $table) {
            $table->unsignedInteger('type_sources_id');
            $table->unsignedInteger('structure_id');
            $table->primary(['type_id','structure_id']);
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
        Schema::dropIfExists('structures_type_sources');
    }
};
