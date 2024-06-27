<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
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

    public function administradores()
    {
        return $this->hasMany(Administrador::class, "id_usuario", "id_usuario");
    }

    // Implementación de JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}