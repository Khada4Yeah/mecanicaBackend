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

        Schema::create("administradores", function (Blueprint $table) use (
            $connection,
        ) {
            $table->id("id_administrador");
            $table->string("clave");

            $table
                ->unsignedBigInteger("id_usuario")
                ->foreign("id_usuario")
                ->references("id_usuario")
                ->on("usuarios");
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
        Schema::dropIfExists("administradores");
    }
};
