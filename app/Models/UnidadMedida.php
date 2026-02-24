<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'unidades_medida';

    protected $fillable = [
        'nombre',
        'abreviatura'
    ];

    /**
     * Relación con materiales
     */
    public function materiales()
    {
        return $this->hasMany(Material::class, 'unidad_medida_id');
    }
}