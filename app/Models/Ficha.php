<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ficha extends Model
{
    use HasFactory;

    protected $table = "fichas";
    protected $primaryKey = "id_ficha";
    public $timestamps = false;

    protected $fillable = ["numero_ficha", "fecha", "otros", "id_vehiculo"];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, "id_vehiculo", "id_vehiculo");
    }

    public function reparaciones()
    {
        return $this->belongsToMany(
            Reparacion::class,
            "fichas_reparaciones",
            "id_ficha",
            "id_reparacion",
        )
            ->using(FichaReparacion::class)
            ->withPivot("informacion_adicional");
    }
}