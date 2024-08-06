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

        Schema::create("usuarios", function (Blueprint $table) use (
            $connection,
        ) {
            $table->id("id_usuario");
            $table->string("cedula", 10)->unique()->index();
            $table->string("nombres");
            $table->string("apellido_p")->nullable();
            $table->string("apellido_m")->nullable();
            $table->string("correo_electronico")->unique();
            $table->string("celular");
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
        Schema::dropIfExists("usuarios");
    }
};
