<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Material;
use App\Models\DetalleMaterial;
use App\Models\Subcategoria;
use App\Models\UnidadMedida;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index()
    {
        $materiales     = Material::with(['subcategoria.categoria', 'unidadMedida'])->orderBy('created_at', 'desc')->get();
        $categorias     = Categoria::orderBy('nombre')->get();
        $subcategorias  = Subcategoria::with('categoria')->orderBy('nombre')->get();
        $unidadesMedida = UnidadMedida::orderBy('nombre')->get();

        return view('materiales.index', compact('materiales', 'categorias', 'subcategorias', 'unidadesMedida'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subcategoria_id'  => 'required|exists:subcategorias,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'codigo'           => 'required|string|max:50|unique:materiales,codigo',
            'nombre'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'estado'           => 'required|in:0,1',
            'cantidad_actual'  => 'required|numeric|min:0',
            'cantidad_minima'  => 'required|numeric|min:0',
            'precio_unitario'  => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $material = Material::create([
                'subcategoria_id'  => $validated['subcategoria_id'],
                'unidad_medida_id' => $validated['unidad_medida_id'],
                'codigo'           => $validated['codigo'],
                'nombre'           => $validated['nombre'],
                'descripcion'      => $validated['descripcion'],
                'estado'           => (bool) $validated['estado'],
            ]);

            DetalleMaterial::create([
                'material_id'     => $material->id,
                'cantidad_actual' => $validated['cantidad_actual'],
                'cantidad_minima' => $validated['cantidad_minima'],
                'precio_unitario' => $validated['precio_unitario'],
                'costo_total'     => $validated['cantidad_actual'] * $validated['precio_unitario'],
            ]);

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Materiales',
                'Crear',
                'Creó el material: "' . $material->nombre . '" - Código: ' . $material->codigo
            );

            DB::commit();
            return redirect()->route('materiales.index')->with('success', 'Material creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear el material: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'subcategoria_id'  => 'required|exists:subcategorias,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'codigo'           => 'required|string|max:50|unique:materiales,codigo,' . $material->id,
            'nombre'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'estado'           => 'required|in:0,1',
        ]);

        DB::beginTransaction();
        try {
            $nombreAnterior = $material->nombre; // guardamos antes de actualizar

            $material->update([
                'subcategoria_id'  => $validated['subcategoria_id'],
                'unidad_medida_id' => $validated['unidad_medida_id'],
                'codigo'           => $validated['codigo'],
                'nombre'           => $validated['nombre'],
                'descripcion'      => $validated['descripcion'],
                'estado'           => (bool) $validated['estado'],
            ]);

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Materiales',
                'Editar',
                'Actualizó el material: "' . $nombreAnterior . '" → "' . $material->nombre . '" - Código: ' . $material->codigo
            );

            DB::commit();
            return redirect()->route('materiales.index')->with('success', 'Material actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Material $material)
    {
        DB::beginTransaction();
        try {
            $nombre = $material->nombre; // guardamos antes de eliminar
            $codigo = $material->codigo;

            $material->delete();

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Materiales',
                'Eliminar',
                'Eliminó el material: "' . $nombre . '" - Código: ' . $codigo
            );

            DB::commit();
            return redirect()->route('materiales.index')->with('success', 'Material eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();

            if (str_contains($e->getMessage(), '23000')) {
                return redirect()->back()->with('error', 'No se puede eliminar el material porque ya tiene movimientos registrados en el sistema.');
            }

            return redirect()->back()->with('error', 'Ocurrió un error al intentar eliminar el registro.');
        }
    }
}