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
        Schema::create('escalas', function (Blueprint $table) {
            
            $table->bigIncrements('id');

            $table->date('dia');

            $table->unsignedBigInteger('id_funcionario')->unsigned();
            $table->foreign('id_funcionario')->references('id')->on('funcionarios');

            $table->unsignedBigInteger('id_setor')->unsigned();
            $table->foreign('id_setor')->references('id')->on('setors');   

            $table->unsignedBigInteger('id_turno');
            $table->foreign('id_turno')->references('id')->on('turnos');    

            $table->enum('status', ['E', 'F', 'D', 'A']); //ESCALADO, FOLGA, DESCANÇO, AUSENTE

            //Cuidado modificar aqui deverá alterar também na controller escalas e outros formulários
            $table->enum('tipoAuse', ['FERIAS', 'LISENCA', 'ATESTADO', 'OUTROS'])->nullable();

            $table->string ('observacao')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalas');
    }
};
