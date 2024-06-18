<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = "clientes";
    protected $primaryKey = "id_cliente";
    public $timestamps = false;

    protected $fillable = ["id_usuario"];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "id_usuario", "id_usuario");
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, "id_cliente", "id_cliente");
    }
}
