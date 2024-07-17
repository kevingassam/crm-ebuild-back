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
        Schema::create('files_taches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tache_id');
            $table->string('file_name');
            $table->string('file_path');
            // Add any additional columns you may need for file metadata
            $table->timestamps();
            $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_taches');
    }
};
