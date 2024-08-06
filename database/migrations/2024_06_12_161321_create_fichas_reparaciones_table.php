<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener el nombre del driver de la conexión de base de datos
        $connection = DB::connection()->getDriverName();

        Schema::create("fichas_reparaciones", function (Blueprint $table) use (
            $connection,
        ) {
            $table->unsignedBigInteger("id_ficha");
            $table->unsignedBigInteger("id_reparacion");
            $table->json("informacion_adicional")->nullable();

            $table->foreign("id_ficha")->references("id_ficha")->on("fichas");
            $table
                ->foreign("id_reparacion")
                ->references("id_reparacion")
                ->on("reparaciones");

            $table->primary(["id_ficha", "id_reparacion"]);
            // Si la conexión es MySQL, establecer el motor de almacenamiento a InnoDB
            if ($connection === "mysql") {
                $table->engine = "InnoDB";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("fichas_reparaciones");
    }
};
