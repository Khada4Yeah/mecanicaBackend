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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
