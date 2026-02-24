<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'movimientos';

    protected $fillable = [
        'material_id',
        'tipo_movimiento',
        'fecha',
        'numero_factura',
        'numero_ingreso',
        'numero_salida',
        'unidad_solicitante',
        'cantidad',
        'costo_unitario',
        'total',
        'saldo_cantidad',
        'saldo_costo_total',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'total' => 'decimal:2',
        'saldo_cantidad' => 'decimal:2',
        'saldo_costo_total' => 'decimal:2',
    ];

    // Relación con Material
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Calcular el total automáticamente
    public static function boot()
    {
        parent::boot();

        static::creating(function ($movimiento) {
            $movimiento->total = $movimiento->cantidad * $movimiento->costo_unitario;
        });

        static::updating(function ($movimiento) {
            $movimiento->total = $movimiento->cantidad * $movimiento->costo_unitario;
        });
    }

    // Scope para filtrar por tipo
    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }

    // Scope para filtrar por material
    public function scopePorMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }

    // Accessor para el tipo de movimiento en español
    public function getTipoMovimientoTextoAttribute()
    {
        return $this->tipo_movimiento === 'entrada' ? 'Entrada' : 'Salida';
    }
}