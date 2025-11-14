<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Muestra un listado de las categorías (jerárquicamente).
     */
    public function index(): View
    {
        // Obtenemos solo las categorías PADRE (las que no tienen parent_id)
        // y cargamos anticipadamente sus 'hijos' (relación 'children')
        $categories = Category::with('children')
                              ->whereNull('parent_id')
                              ->orderBy('type')
                              ->orderBy('name')
                              ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create(): View
    {
        // Pasamos todas las categorías a la vista para poder
        // seleccionar una "Categoría Padre" en un dropdown.
        $categories = Category::orderBy('name')->get();
        return view('categories.create', compact('categories'));
    }

    /**
     * Almacena la nueva categoría en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validación
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:ingreso,gasto',
            // 'parent_id' es opcional (nullable) y debe existir en la tabla 'categories'
            'parent_id' => 'nullable|exists:categories,id' 
        ]);

        // 2. Creación
        Category::create($validatedData);

        // 3. Redirección
        return redirect()->route('categorias.index')
                         ->with('success', '¡Categoría creada con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario para editar una categoría existente.
     * * Laravel automáticamente encontrará la $category usando su ID (Route Model Binding).
     */
    public function edit(string $id): View
    {
        // Necesitamos todas las categorías para el dropdown de "Categoría Padre"
        // Excluimos la categoría actual, ya que no puede ser padre de sí misma.
        $categories = Category::where('id', '!=', $category->id)->orderBy('name')->get();

        // Pasamos la categoría específica Y la lista de categorías a la vista
        return view('categories.edit', compact('category', 'categories'));
    }

    /**
     * Actualiza la categoría en la base de datos.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // 1. Validación (similar a 'store', pero 'name' puede ser único)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:ingreso,gasto',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        // 2. Actualizamos el modelo
        $category->update($validatedData);

        // 3. Redirección
        return redirect()->route('categorias.index')
                         ->with('success', '¡Categoría actualizada con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
