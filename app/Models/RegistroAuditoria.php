<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroAuditoria extends Model
{
    public $timestamps = false; // usamos campo 'fecha' propio

    protected $table = 'registro_auditoria';

    protected $fillable = [
        'usuario_id',
        'modulo',
        'accion',
        'descripcion',
        'ip',
        'fecha'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}