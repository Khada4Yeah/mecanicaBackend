<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("fichas", function (Blueprint $table) {
            $table->id("id_ficha");
            $table->integer("numero_ficha");
            $table->date("fecha");
            $table->text("otros")->nullable();

            $table->bigInteger("id_cliente");
            $table
                ->foreign("id_cliente")
                ->references("id_cliente")
                ->on("clientes");
            $table->bigInteger("id_vehiculo");
            $table
                ->foreign("id_vehiculo")
                ->references("id_vehiculo")
                ->on("vehiculos");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("fichas");
    }
};
