<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Proveedor;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::query()
            ->orderByDesc('estatus')
            ->orderBy('razon_social')
            ->paginate(12);

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

        $validated['estatus'] = true;

        $proveedor = Proveedor::create($validated); // El Trait Auditable llena automáticamente el usuario

        AdminAudit::record(
            $request,
            $proveedor,
            'proveedor_creado',
            ['payload' => Arr::except($validated, ['estatus'])]
        );

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

        $original = Arr::only($proveedor->getOriginal(), [
            'no_proveedor',
            'rfc',
            'razon_social',
            'direccion',
            'telefono',
            'correo',
            'pagina_web',
            'representante',
            'estatus',
        ]);

        $proveedor->update($validated);

        $changes = $this->detectChanges($original, $proveedor->only(array_keys($original)));

        if ($changes) {
            AdminAudit::record(
                $request,
                $proveedor,
                'proveedor_actualizado',
                ['changes' => $changes]
            );
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function deactivate(Request $request, Proveedor $proveedor)
    {
        $user = $request->user();
        $role = $user?->role();

        if (! $user || (! $user->is_super_admin && $role !== UserRole::ADMIN_FARMACIA)) {
            abort(403);
        }

        if (! $proveedor->estatus) {
            return back()->with('success', 'El proveedor ya estaba inactivo.');
        }

        $proveedor->update([
            'estatus' => false,
            'last_modified_by_user_id' => $user->id,
        ]);

        AdminAudit::record(
            $request,
            $proveedor,
            'proveedor_desactivado',
            ['previous_state' => ['estatus' => true]]
        );

        return redirect()->route('proveedores.index')->with('success', 'Proveedor desactivado correctamente.');
    }

    public function activate(Request $request, Proveedor $proveedor)
    {
        $user = $request->user();
        $role = $user?->role();

        if (! $user || (! $user->is_super_admin && $role !== UserRole::SUPER_ADMIN)) {
            abort(403);
        }

        if ($proveedor->estatus) {
            return back()->with('success', 'El proveedor ya estaba activo.');
        }

        $proveedor->update([
            'estatus' => true,
            'last_modified_by_user_id' => $user->id,
        ]);

        AdminAudit::record(
            $request,
            $proveedor,
            'proveedor_reactivado',
            ['previous_state' => ['estatus' => false]]
        );

        return redirect()->route('proveedores.index')->with('success', 'Proveedor reactivado correctamente.');
    }

    public function destroy(Request $request, Proveedor $proveedor)
    {
        $user = $request->user();
        $role = $user?->role();

        if (! $user || (! $user->is_super_admin && $role !== UserRole::SUPER_ADMIN)) {
            abort(403);
        }

        $validator = Validator::make(
            $request->all(),
            ['confirmation' => ['required', 'string', 'in:ELIMINAR']],
            [],
            ['confirmation' => 'confirmación']
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'deleteProveedor')
                ->with('delete_proveedor_id', $proveedor->id)
                ->withInput();
        }

        if ($proveedor->estatus) {
            return back()->with('error', 'Desactiva el proveedor antes de eliminarlo permanentemente.');
        }

        if ($proveedor->lotes()->exists()) {
             return back()->with('error', 'No se puede eliminar el proveedor porque tiene historial de lotes.');
        }

        AdminAudit::record(
            $request,
            $proveedor,
            'proveedor_eliminado',
            [
                'no_proveedor' => $proveedor->no_proveedor,
                'rfc' => $proveedor->rfc,
            ]
        );

        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }

    private function detectChanges(array $original, array $current): array
    {
        $changes = [];

        foreach ($original as $field => $oldValue) {
            $newValue = $current[$field] ?? null;

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}
