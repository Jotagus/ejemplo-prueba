<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';

    protected $fillable = [
        'subcategoria_id',
        'unidad_medida_id',
        'codigo',
        'nombre',
        'estado',
        'descripcion'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function detalleMaterial()
    {
        return $this->hasOne(DetalleMaterial::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function getCategoriaAttribute()
    {
        return $this->subcategoria->categoria ?? null;
    }
}