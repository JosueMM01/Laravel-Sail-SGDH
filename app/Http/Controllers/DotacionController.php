<?php

namespace App\Http\Controllers;

use App\Models\Dotacion;
use App\Models\Area;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DotacionController extends Controller
{
    public function index(Request $request)
    {
        // Permitir filtrar por área para facilitar la visualización
        $query = Dotacion::with(['area', 'producto']);
        
        if ($request->has('area_id') && $request->area_id) {
            $query->where('area_id', $request->area_id);
        }

        $dotaciones = $query->orderBy('area_id')->paginate(20);
        $areas = Area::all(); // Para el filtro en la vista

        return view('dotaciones.index', compact('dotaciones', 'areas'));
    }

    public function create()
    {
        $areas = Area::all();
        $productos = Producto::orderBy('descripcion')->get();
        return view('dotaciones.create', compact('areas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'producto_id' => [
                'required',
                'exists:productos,id',
                // Validación compuesta: unique para la combinación area_id + producto_id
                Rule::unique('dotaciones')->where(function ($query) use ($request) {
                    return $query->where('area_id', $request->area_id)
                                 ->where('producto_id', $request->producto_id);
                }),
            ],
            'cantidad_diaria' => 'required|integer|min:1',
        ], [
            'producto_id.unique' => 'Este producto ya tiene una dotación asignada para esta área.',
        ]);

        Dotacion::create($request->all());

        return redirect()->route('dotaciones.index')
            ->with('success', 'Regla de dotación creada correctamente.');
    }

    public function edit(Dotacion $dotacion)
    {
        $areas = Area::all();
        // No permitimos cambiar el producto en edit, solo la cantidad, para simplificar
        return view('dotaciones.edit', compact('dotacion', 'areas'));
    }

    public function update(Request $request, Dotacion $dotacion)
    {
        $request->validate([
            'cantidad_diaria' => 'required|integer|min:1',
        ]);

        $dotacion->update($request->only('cantidad_diaria'));

        return redirect()->route('dotaciones.index')
            ->with('success', 'Cantidad de dotación actualizada.');
    }

    public function destroy(Dotacion $dotacion)
    {
        $dotacion->delete();
        return back()->with('success', 'Regla de dotación eliminada.');
    }
}