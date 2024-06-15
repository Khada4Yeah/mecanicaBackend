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
}
