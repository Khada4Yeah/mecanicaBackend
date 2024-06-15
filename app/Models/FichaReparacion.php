<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaReparacion extends Model
{
    use HasFactory;

    protected $table = "fichas_reparaciones";
    public $timestamps = false;
    // Deshabilitar las claves primarias autoincrementales
    public $incrementing = false;

    protected $fillable = [
        "id_ficha",
        "id_reparacion",
        "informacion_adicional",
    ];

    // Sobrescribir la propiedad primaryKey para manejar la clave compuesta
    protected $primaryKey = ["id_ficha", "id_reparacion"];

    // Sobrescribir el método setKeysForSaveQuery para manejar la clave compuesta
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, "=", $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        return $this->original[$keyName] ?? $this->getAttribute($keyName);
    }
}
