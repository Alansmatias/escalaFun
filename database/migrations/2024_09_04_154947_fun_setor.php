<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('funSetor', function (Blueprint $table) {
            $table->unsignedBigInteger('id_funcionario')->unsigned();;
            $table->foreign('id_funcionario')->references('id')->on('funcionarios');

            $table->unsignedBigInteger('id_setor')->unsigned();;
            $table->foreign('id_setor')->references('id')->on('setors');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funSetor');
    }
};
