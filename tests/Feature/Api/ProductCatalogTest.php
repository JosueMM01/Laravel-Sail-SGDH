<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Dotacion;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_personal_area_recibe_catalogo_filtrado_por_dotacion(): void
    {
        $areaAsignada = Area::create(['nombre' => 'Urgencias']);
        $otraArea = Area::create(['nombre' => 'Quirófano']);

        $productoAsignado = Producto::create([
            'clave' => 'PROD-A',
            'descripcion' => 'Guantes de látex',
            'presentacion' => 'Caja con 100 piezas',
            'cuadro_basico' => true,
            'stock_min' => 5,
            'stock_optimo' => 10,
            'stock_max' => 20,
        ]);

        $productoExterno = Producto::create([
            'clave' => 'PROD-B',
            'descripcion' => 'Gasas estériles',
            'presentacion' => 'Paquete con 50 piezas',
            'cuadro_basico' => false,
            'stock_min' => 3,
            'stock_optimo' => 6,
            'stock_max' => 12,
        ]);

        Dotacion::create([
            'area_id' => $areaAsignada->id,
            'producto_id' => $productoAsignado->id,
            'cantidad_diaria' => 15,
        ]);

        Dotacion::create([
            'area_id' => $otraArea->id,
            'producto_id' => $productoExterno->id,
            'cantidad_diaria' => 8,
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $areaAsignada->id,
            'is_active' => true,
        ]);

        Sanctum::actingAs($user, [
            'solicitudes:view',
            'solicitudes:create',
            'notifications:view',
            'productos:view',
            'dotacion:view',
        ]);

        $this->getJson('/api/productos')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.producto.id', $productoAsignado->id)
            ->assertJsonPath('data.0.dotacion.cantidad_diaria', 15);
    }

    public function test_listado_requiere_ability(): void
    {
        $area = Area::create(['nombre' => 'Pediatría']);

        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        Sanctum::actingAs($user, ['solicitudes:view']);

        $this->getJson('/api/productos')->assertForbidden();
    }

    public function test_usuario_sin_area_recibe_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => null,
            'is_active' => true,
        ]);

        Sanctum::actingAs($user, [
            'solicitudes:view',
            'solicitudes:create',
            'notifications:view',
            'productos:view',
        ]);

        $this->getJson('/api/productos')->assertStatus(422);
    }
}
