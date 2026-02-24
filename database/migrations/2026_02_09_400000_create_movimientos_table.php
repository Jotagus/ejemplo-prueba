<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materiales')->restrictOnDelete();
            $table->enum('tipo_movimiento', ['entrada', 'salida']); // Tipo de movimiento
            $table->date('fecha');
            
            // Campos para ENTRADA
            $table->string('numero_factura')->nullable(); // N° Factura
            $table->string('numero_ingreso')->nullable(); // N° Ingreso
            
            // Campos para SALIDA
            $table->string('numero_salida')->nullable(); // N° Salida
            $table->string('unidad_solicitante')->nullable(); // Departamento/Persona que solicita
            
            // Campos comunes
            $table->decimal('cantidad', 10, 2);
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('total', 10, 2);
            
            // Saldos después del movimiento
            $table->decimal('saldo_cantidad', 10, 2);
            $table->decimal('saldo_costo_total', 10, 2);
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos');
    }
};