<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleMaterial extends Model
{
    use HasFactory;

    protected $table = 'detalle_material';

    protected $fillable = [
        'material_id',
        'cantidad_actual',
        'cantidad_minima',
        'precio_unitario',
        'costo_total'
    ];

    protected $casts = [
        'cantidad_actual' => 'decimal:2',
        'cantidad_minima' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}