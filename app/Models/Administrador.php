<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    use HasFactory;

    protected $table = "administradores";

    protected $primaryKey = "id_administrador";

    protected $fillable = ["id_usuario"];

    protected $hidden = ["clave"];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "id_usuario", "id_usuario");
    }
}
