<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = "usuarios";
    protected $primaryKey = "id_usuario";
    protected $dateFormat = "Y-m-d\TH:i:s";

    protected $fillable = [
        "cedula",
        "apellido_p",
        "apellido_m",
        "nombres",
        "correo_electronico",
        "celular",
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, "id_usuario", "id_usuario");
    }
}
