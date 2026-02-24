<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use App\Models\Categoria;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
{
    public function index()
    {
        $subcategorias = Subcategoria::with('categoria')->get();
        $categorias    = Categoria::all();

        return view('subcategorias.index', compact('subcategorias', 'categorias'));
    }

    public function create()
    {
        //$categorias = Categoria::all();
        //return view('subcategorias.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string'
        ]);

        $subcategoria = Subcategoria::create($request->all());

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Subcategorías',
            'Crear',
            'Creó la subcategoría: "' . $subcategoria->nombre . '" en la categoría: "' . $subcategoria->categoria->nombre . '"'
        );

        return redirect()->route('categorias.index')->with('success', 'Subcategoría creada');
    }

    public function edit(Subcategoria $subcategoria)
    {
        //$categorias = Categoria::all();
        //return view('subcategorias.edit', compact('subcategoria', 'categorias'));
    }

    public function update(Request $request, Subcategoria $subcategoria)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string'
        ]);

        $nombreAnterior    = $subcategoria->nombre;
        $categoriaNombre   = $subcategoria->categoria->nombre;

        $subcategoria->update($request->all());

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Subcategorías',
            'Editar',
            'Actualizó la subcategoría: "' . $nombreAnterior . '" → "' . $subcategoria->nombre . '" en la categoría: "' . $categoriaNombre . '"'
        );

        return redirect()->route('categorias.index')->with('success', 'Subcategoría actualizada exitosamente');
    }

    public function destroy(Subcategoria $subcategoria)
    {
        $nombre          = $subcategoria->nombre;
        $categoriaNombre = $subcategoria->categoria->nombre;

        $subcategoria->delete();

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Subcategorías',
            'Eliminar',
            'Eliminó la subcategoría: "' . $nombre . '" de la categoría: "' . $categoriaNombre . '"'
        );

        return redirect()->route('categorias.index')->with('success', 'Subcategoría eliminada exitosamente');
    }
}