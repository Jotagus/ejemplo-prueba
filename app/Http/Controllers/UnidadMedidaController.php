<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    /**
     * Mostrar listado de unidades
     */
    public function index()
    {
        $unidades = UnidadMedida::orderBy('nombre')->get();
        return view('unidades.index', compact('unidades'));
    }

    /**
     * Mostrar formulario para crear una nueva unidad
     */
    public function create()
    {
        return view('unidades.create');
    }

    /**
     * Guardar nueva unidad
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:unidades_medida,nombre',
            'abreviatura' => 'required|string|max:10|unique:unidades_medida,abreviatura',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Esta unidad de medida ya existe',
            'abreviatura.required' => 'La abreviatura es obligatoria',
            'abreviatura.unique' => 'Esta abreviatura ya está en uso',
        ]);

        UnidadMedida::create([
            'nombre' => $request->nombre,
            'abreviatura' => $request->abreviatura,
        ]);

        return redirect()->route('materiales.index')
            ->with('success', 'Unidad de medida "' . $request->nombre . '" creada exitosamente');
    }

    /**
     * Actualizar unidad existente
     */
    public function update(Request $request, UnidadMedida $unidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:unidades_medida,nombre,' . $unidad->id,
            'abreviatura' => 'required|string|max:10|unique:unidades_medida,abreviatura,' . $unidad->id,
        ]);

        $unidad->update([
            'nombre' => $request->nombre,
            'abreviatura' => $request->abreviatura,
        ]);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad de medida actualizada exitosamente');
    }

    /**
     * Eliminar unidad
     */
    public function destroy(UnidadMedida $unidad)
    {
        try {
            $unidad->delete();
            return redirect()->route('unidades.index')
                ->with('success', 'Unidad de medida eliminada exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('unidades.index')
                ->with('error', 'No se puede eliminar la unidad porque está siendo usada por materiales');
        }
    }
}