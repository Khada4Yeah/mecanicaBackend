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
        Schema::create("vehiculos", function (Blueprint $table) {
            $table->id("id_vehiculo");
            $table->string("placa")->unique()->index();
            $table->string("marca");
            $table->string("modelo");
            $table->string("chasis")->unique();
            $table->string("motor")->unique();

            $table->bigInteger("id_cliente");
            $table
                ->foreign("id_cliente")
                ->references("id_cliente")
                ->on("clientes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("vehiculos");
    }
};