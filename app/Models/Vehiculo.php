<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = "vehiculos";
    protected $primaryKey = "id_vehiculo";
    protected $dateFormat = "Y-m-d\TH:i:s";

    protected $fillable = [
        "placa",
        "marca",
        "modelo",
        "chasis",
        "motor",
        "id_cliente",
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, "id_cliente", "id_cliente");
    }

    public function fichas()
    {
        return $this->hasMany(
            FichaReparacion::class,
            "id_vehiculo",
            "id_vehiculo",
        );
    }
}
