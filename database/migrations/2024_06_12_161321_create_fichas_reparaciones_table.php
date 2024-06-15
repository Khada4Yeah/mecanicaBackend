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
        Schema::create('fichas_reparaciones', function (Blueprint $table) {
            $table->unsignedBigInteger("id_ficha");
            $table->unsignedBigInteger("id_reparacion");
            $table->json("informacion_adicional")->nullable();

            $table
                ->foreign("id_ficha")
                ->references("id_ficha")
                ->on("fichas");
            $table->foreign("id_reparacion")
                ->references("id_reparacion")
                ->on("reparaciones");

            $table->primary(["id_ficha", "id_reparacion"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichas_reparaciones');
    }
};
