<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    use HasFactory;

    protected $table = "reparaciones";
    protected $primaryKey = "id_reparacion";
    public $timestamps = false;

    protected $fillable = ["tipo_reparacion"];

    public function fichas()
    {
        return $this->belongsToMany(
            Ficha::class,
            "fichas_reparaciones",
            "id_reparacion",
            "id_ficha",
        )
            ->using(FichaReparacion::class)
            ->withPivot("informacion_adicional");
    }
}