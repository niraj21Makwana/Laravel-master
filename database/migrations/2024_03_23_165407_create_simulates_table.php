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
        Schema::create('simulates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prize_id');
            $table->decimal('percentage_of_winners',10,2)->default(0);
            $table->timestamps();
            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simulates');
    }
};
