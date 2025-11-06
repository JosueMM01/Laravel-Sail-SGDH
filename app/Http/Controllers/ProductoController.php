<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all(); // Podrías usar paginación: Proveedor::paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_proveedor' => 'required|unique:proveedores,no_proveedor|max:50',
            'rfc' => 'required|unique:proveedores,rfc|size:13|regex:/^[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]$/i', // Validación básica de RFC
            'razon_social' => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'pagina_web' => 'nullable|url|max:255',
            'representante' => 'nullable|string|max:255',
        ]);

        Proveedor::create($validated); // El Trait Auditable llena automáticamente el usuario

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'no_proveedor' => 'required|max:50|unique:proveedores,no_proveedor,' . $proveedor->id,
            'rfc' => 'required|size:13|regex:/^[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]$/i|unique:proveedores,rfc,' . $proveedor->id,
            'razon_social' => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'pagina_web' => 'nullable|url|max:255',
            'representante' => 'nullable|string|max:255',
            'estatus' => 'boolean', // Permitir activar/desactivar
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        // Validar si tiene lotes asociados antes de eliminar
        if ($proveedor->lotes()->exists()) {
             return back()->with('error', 'No se puede eliminar el proveedor porque tiene historial de lotes.');
        }

        $proveedor->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}