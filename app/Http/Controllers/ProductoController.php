<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Producto;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::query()
            ->withCount(['lotes as lotes_count' => function ($query) {
                $query->where('cantidad_actual', '>', 0)
                    ->whereDate('fecha_caducidad', '>=', now());
            }])
            ->orderByDesc('is_active')
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
            'image' => 'nullable|image|max:2048',
            'cuadro_basico' => 'nullable|boolean',
            'stock_min' => 'required|integer|min:0',
            'stock_optimo' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
        ]);

        $validated['cuadro_basico'] = $request->boolean('cuadro_basico');

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $this->storeProductImage($request->file('image'), $validated['descripcion']);
        }

        unset($validated['image']);

        $validated['image_path'] = $imagePath;
        $validated['is_active'] = true;
        $validated['last_modified_by_user_id'] = $request->user()?->id;

        $producto = Producto::create($validated);

        AdminAudit::record(
            $request,
            $producto,
            'producto_creado',
            ['payload' => Arr::except($validated, ['image_path'])]
        );

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
            'image' => 'nullable|image|max:2048',
            'cuadro_basico' => 'nullable|boolean',
            'stock_min' => 'required|integer|min:0',
            'stock_optimo' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
            'remove_image' => 'nullable|boolean',
        ]);

        $validated['cuadro_basico'] = $request->boolean('cuadro_basico');

        $imagePath = $producto->image_path;

        if ($request->boolean('remove_image') && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $this->storeProductImage($request->file('image'), $validated['descripcion']);
        }

        unset($validated['image'], $validated['remove_image']);

        $validated['image_path'] = $imagePath;
        $validated['last_modified_by_user_id'] = $request->user()?->id;

        $original = Arr::only($producto->getOriginal(), [
            'clave',
            'descripcion',
            'presentacion',
            'cuadro_basico',
            'stock_min',
            'stock_optimo',
            'stock_max',
            'is_active',
            'image_path',
        ]);

        $producto->update($validated);

        $changes = $this->detectChanges($original, $producto->only(array_keys($original)));

        if ($changes) {
            AdminAudit::record(
                $request,
                $producto,
                'producto_actualizado',
                ['changes' => $changes]
            );
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function deactivate(Request $request, Producto $producto)
    {
        $user = $request->user();
        $role = $user?->role();

        if (! $user || (! $user->is_super_admin && $role !== UserRole::ADMIN_FARMACIA)) {
            abort(403);
        }

        if (! $producto->is_active) {
            return back()->with('success', 'El producto ya se encontraba inactivo.');
        }

        $producto->update([
            'is_active' => false,
            'last_modified_by_user_id' => $user->id,
        ]);

        AdminAudit::record(
            $request,
            $producto,
            'producto_desactivado',
            ['previous_state' => ['is_active' => true]]
        );

        return redirect()->route('productos.index')->with('success', 'Producto desactivado correctamente.');
    }

    public function activate(Request $request, Producto $producto)
    {
        $user = $request->user();
        $role = $user?->role();

        if (! $user || (! $user->is_super_admin && $role !== UserRole::SUPER_ADMIN)) {
            abort(403);
        }

        if ($producto->is_active) {
            return back()->with('success', 'El producto ya se encontraba activo.');
        }

        $producto->update([
            'is_active' => true,
            'last_modified_by_user_id' => $user->id,
        ]);

        AdminAudit::record(
            $request,
            $producto,
            'producto_reactivado',
            ['previous_state' => ['is_active' => false]]
        );

        return redirect()->route('productos.index')->with('success', 'Producto reactivado correctamente.');
    }

    public function destroy(Request $request, Producto $producto)
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
                ->withErrors($validator, 'deleteProducto')
                ->with('delete_producto_id', $producto->id)
                ->withInput();
        }

        if ($producto->is_active) {
            return back()->with('error', 'Desactiva el producto antes de eliminarlo de forma definitiva.');
        }

        if ($producto->lotes()->exists()) {
            return back()->with('error', 'No se puede eliminar el producto porque tiene lotes asociados.');
        }

        $metadata = [
            'clave' => $producto->clave,
            'presentacion' => $producto->presentacion,
            'stock_total' => $producto->stock_total,
        ];

        AdminAudit::record(
            $request,
            $producto,
            'producto_eliminado',
            $metadata
        );

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

    private function storeProductImage(UploadedFile $file, string $descripcion): string
    {
        $disk = Storage::disk('public');

        $baseName = Str::of($descripcion)
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9]+/', '_')
            ->trim('_');

        if ($baseName->isEmpty()) {
            $baseName = Str::of('producto');
        }

        $baseName = (string) $baseName;

        if (strlen($baseName) > 20) {
            $baseName = substr($baseName, 0, 20);
            $baseName = trim($baseName, '_');
        }

        if ($baseName === '') {
            $baseName = 'producto';
        }

        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
        $extension = strtolower($extension);

        $filename = $baseName . '.' . $extension;
        $path = 'productos/' . $filename;
        $counter = 1;

        while ($disk->exists($path)) {
            $suffix = '_' . $counter;
            $trimmedBase = $baseName;

            if (strlen($trimmedBase . $suffix) > 20) {
                $trimmedBase = substr($trimmedBase, 0, max(1, 20 - strlen($suffix)));
                $trimmedBase = trim($trimmedBase, '_');
            }

            if ($trimmedBase === '') {
                $trimmedBase = 'producto';
            }

            $filename = $trimmedBase . $suffix . '.' . $extension;
            $path = 'productos/' . $filename;
            $counter++;
        }

        $disk->putFileAs('productos', $file, $filename);

        return $path;
    }

    private function detectChanges(array $original, array $current): array
    {
        $changes = [];

        foreach ($original as $field => $oldValue) {
            $newValue = $current[$field] ?? null;

            if ($oldValue instanceof \JsonSerializable) {
                $oldValue = $oldValue->jsonSerialize();
            }

            if ($newValue instanceof \JsonSerializable) {
                $newValue = $newValue->jsonSerialize();
            }

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