<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::query()
            ->withCount(['lotes as lotes_count' => function ($query) {
                $query->where('cantidad_actual', '>', 0)
                    ->whereDate('fecha_caducidad', '>=', now());
            }])
            ->orderBy('descripcion')
            ->paginate(12);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clave' => 'required|string|max:50|unique:productos,clave',
            'descripcion' => 'required|string',
            'presentacion' => 'required|string|max:255',
            'cuadro_basico' => 'nullable|boolean',
            'stock_min' => 'required|integer|min:0',
            'stock_optimo' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
        ]);

        $validated['cuadro_basico'] = $request->boolean('cuadro_basico');

        Producto::create($validated);

        return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['lotes' => function ($query) {
            $query->with('proveedor')
                ->where('cantidad_actual', '>', 0)
                ->whereDate('fecha_caducidad', '>=', now())
                ->orderBy('fecha_caducidad');
        }]);

        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'clave' => 'required|string|max:50|unique:productos,clave,' . $producto->id,
            'descripcion' => 'required|string',
            'presentacion' => 'required|string|max:255',
            'cuadro_basico' => 'nullable|boolean',
            'stock_min' => 'required|integer|min:0',
            'stock_optimo' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
        ]);

        $validated['cuadro_basico'] = $request->boolean('cuadro_basico');

        $producto->update($validated);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->lotes()->exists()) {
            return back()->with('error', 'No se puede eliminar el producto porque tiene lotes asociados.');
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}