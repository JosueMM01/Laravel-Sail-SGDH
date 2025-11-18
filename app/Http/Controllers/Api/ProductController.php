<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DotacionProductoResource;
use App\Models\Dotacion;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('productos:view')) {
            abort(403, __('No tienes permisos para consultar productos.'));
        }

        if (! $user->area_id) {
            throw ValidationException::withMessages([
                'area' => __('Tu cuenta no tiene un área asignada.'),
            ]);
        }

        $perPage = min(max((int) $request->query('per_page', 50), 1), 100);
        $search = trim((string) $request->query('search', ''));

        $query = Dotacion::query()
            ->where('area_id', $user->area_id)
            ->with(['producto' => function ($builder) {
                $builder->withSum(['lotes as stock_disponible' => function ($lotQuery) {
                    $lotQuery
                        ->where('cantidad_actual', '>', 0)
                        ->whereDate('fecha_caducidad', '>=', now());
                }], 'cantidad_actual');
            }])
            ->orderBy('producto_id');

        if ($search !== '') {
            $query->whereHas('producto', function ($builder) use ($search): void {
                $builder
                    ->where('clave', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $dotaciones = $query->paginate($perPage)->appends($request->query());

        return DotacionProductoResource::collection($dotaciones);
    }
}
