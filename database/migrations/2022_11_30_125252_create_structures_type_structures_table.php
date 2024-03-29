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
        Schema::create('structures_type_structures', function (Blueprint $table) {
            $table->unsignedInteger('structure_id');
            $table->unsignedInteger('type_structure_id');
            $table->primary(['structure_id','type_structure_id'],'structure_type_structure_id');
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
        Schema::dropIfExists('structures_type_structures');
    }
};
