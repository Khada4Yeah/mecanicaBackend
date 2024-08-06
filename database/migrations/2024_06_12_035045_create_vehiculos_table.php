<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Add this line to import the DB facade

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener el nombre del driver de la conexión de base de datos
        $connection = DB::connection()->getDriverName();

        Schema::create("vehiculos", function (Blueprint $table) use (
            $connection,
        ) {
            $table->id("id_vehiculo");
            $table->string("placa")->unique()->index();
            $table->string("marca");
            $table->string("modelo");
            $table->string("chasis")->unique();
            $table->string("motor")->unique();

            $table->unsignedBigInteger("id_cliente");
            $table
                ->foreign("id_cliente")
                ->references("id_cliente")
                ->on("clientes");
            $table->timestamps();
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
        Schema::dropIfExists("vehiculos");
    }
};
