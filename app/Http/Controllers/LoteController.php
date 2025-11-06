<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index() {
        // Eager loading para optimizar consultas
        $lotes = Lote::with(['producto', 'proveedor'])->orderBy('fecha_caducidad')->get();
        return view('lotes.index', compact('lotes'));
    }

    public function create() {
        $productos = Producto::all();
        $proveedores = Proveedor::where('estatus', true)->get();
        return view('lotes.create', compact('productos', 'proveedores'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'numero_lote' => 'required|string',
            'fecha_caducidad' => 'required|date',
            'cantidad_recibida' => 'required|integer|min:1',
            'fecha_compra' => 'required|date',
        ]);
        
        // Al crear, la cantidad actual es igual a la recibida
        $validated['cantidad_actual'] = $validated['cantidad_recibida'];
        
        Lote::create($validated);
        return redirect()->route('lotes.index')->with('success', 'Lote registrado correctamente.');
    }
    // Edit/Update/Destroy similares al CRUD base si necesitas corregir errores de captura.
}