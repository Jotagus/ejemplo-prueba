<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        $subcategorias = Subcategoria::with('categoria')->get();

        return view('categorias.index', compact('categorias', 'subcategorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $categoria = Categoria::create($request->all());

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Categorías',
            'Crear',
            'Creó la categoría: ' . $categoria->nombre
        );

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente');
    }

    public function edit(Categoria $categoria)
    {
        // return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $nombreAnterior = $categoria->nombre; // guardamos el nombre antes de actualizar

        $categoria->update($request->all());

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Categorías',
            'Editar',
            'Actualizó la categoría: "' . $nombreAnterior . '" → "' . $categoria->nombre . '"'
        );

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->subcategorias()->exists()) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar porque tiene subcategorías asociadas.');
        }

        $nombre = $categoria->nombre; // guardamos el nombre antes de eliminar

        $categoria->delete();

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Categorías',
            'Eliminar',
            'Eliminó la categoría: ' . $nombre
        );

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}