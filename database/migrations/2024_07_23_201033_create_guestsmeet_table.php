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
    Schema::create('guestsmeet', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('meet_id');
        $table->unsignedBigInteger('personnel_id')->nullable();
        $table->unsignedBigInteger('client_id')->nullable();
        $table->timestamps();
        $table->foreign('meet_id')->references('id')->on('meet')->onDelete('cascade');
        $table->foreign('personnel_id')->references('id')->on('personnel')->onDelete('set null');
        $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guestsmeet');
    }
};
