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

    protected $fillable = [
        "numero_ficha",
        "fecha",
        "otros",
        "id_cliente",
        "id_vehiculo",
    ];
}
