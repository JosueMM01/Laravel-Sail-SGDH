<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Dotacion;
use App\Models\Entrega;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DotacionFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_fulfill_dotacion_and_discount_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $area = Area::create(['nombre' => 'Urgencias']);

        $producto = Producto::create([
            'clave' => 'MED-001',
            'descripcion' => 'Paracetamol 500mg',
            'presentacion' => 'Caja 20 tabletas',
            'cuadro_basico' => true,
            'stock_min' => 10,
            'stock_max' => 100,
            'stock_optimo' => 60,
        ]);

        $proveedor = Proveedor::create([
            'no_proveedor' => 'PRV-001',
            'rfc' => 'XAXX010101000',
            'razon_social' => 'Proveedor General',
            'estatus' => true,
        ]);

        $dotacion = Dotacion::create([
            'area_id' => $area->id,
            'producto_id' => $producto->id,
            'cantidad_diaria' => 50,
        ]);

        $caducidadBase = now()->addMonths(6);

        Lote::create([
            'producto_id' => $producto->id,
            'proveedor_id' => $proveedor->id,
            'numero_lote' => 'L-001',
            'fecha_caducidad' => $caducidadBase,
            'cantidad_recibida' => 30,
            'cantidad_actual' => 30,
            'fecha_compra' => now()->subMonth(),
        ]);

        Lote::create([
            'producto_id' => $producto->id,
            'proveedor_id' => $proveedor->id,
            'numero_lote' => 'L-002',
            'fecha_caducidad' => $caducidadBase->copy()->addMonth(),
            'cantidad_recibida' => 40,
            'cantidad_actual' => 40,
            'fecha_compra' => now()->subMonth(),
        ]);

        $this->post(route('dotaciones.fulfill', $area), [
            'cantidades' => [
                $dotacion->id => 50,
            ],
        ])->assertRedirect(route('dotaciones.index', ['area_id' => $area->id]));

        $this->assertDatabaseHas('entregas', [
            'area_id' => $area->id,
            'tipo_entrega' => 'surtido_diario',
        ]);

        $entrega = Entrega::where('area_id', $area->id)->where('tipo_entrega', 'surtido_diario')->first();
        $this->assertNotNull($entrega);
        $this->assertEquals(50, $entrega->detalles()->sum('cantidad_entregada'));
        $this->assertEquals(20, Lote::where('numero_lote', 'L-002')->value('cantidad_actual'));
        $this->assertEquals(0, Lote::where('numero_lote', 'L-001')->value('cantidad_actual'));

        $observaciones = $entrega->observaciones;
        $this->assertIsArray($observaciones);
        $this->assertEquals(50, $observaciones['resumen']['total_entregado'] ?? 0);
        $this->assertEquals(0, $observaciones['resumen']['total_faltante'] ?? -1);
        $this->assertEquals([], $observaciones['alertas'] ?? []);
    }

    public function test_registers_motivo_when_stock_is_not_enough(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $area = Area::create(['nombre' => 'Quirófano']);

        $producto = Producto::create([
            'clave' => 'MED-010',
            'descripcion' => 'Gasas estériles',
            'presentacion' => 'Paquete 10 piezas',
            'cuadro_basico' => true,
            'stock_min' => 5,
            'stock_max' => 80,
            'stock_optimo' => 40,
        ]);

        $proveedor = Proveedor::create([
            'no_proveedor' => 'PRV-002',
            'rfc' => 'XEXX010101000',
            'razon_social' => 'Suministros Médicos',
            'estatus' => true,
        ]);

        $dotacion = Dotacion::create([
            'area_id' => $area->id,
            'producto_id' => $producto->id,
            'cantidad_diaria' => 60,
        ]);

        Lote::create([
            'producto_id' => $producto->id,
            'proveedor_id' => $proveedor->id,
            'numero_lote' => 'G-001',
            'fecha_caducidad' => now()->addMonths(3),
            'cantidad_recibida' => 30,
            'cantidad_actual' => 30,
            'fecha_compra' => now()->subWeeks(2),
        ]);

        $motivo = 'Stock insuficiente, se surtirá en la próxima recepción.';

        $this->post(route('dotaciones.fulfill', $area), [
            'cantidades' => [
                $dotacion->id => 60,
            ],
            'motivos' => [
                $dotacion->id => $motivo,
            ],
        ])->assertRedirect(route('dotaciones.index', ['area_id' => $area->id]));

        $entrega = Entrega::where('area_id', $area->id)->where('tipo_entrega', 'surtido_diario')->first();
        $this->assertNotNull($entrega);

        $observaciones = $entrega->observaciones;
        $this->assertEquals(30, $observaciones['resumen']['total_entregado'] ?? -1);
        $this->assertEquals(30, $observaciones['resumen']['total_faltante'] ?? -1);
        $this->assertNotEmpty($observaciones['alertas'] ?? []);

        $item = collect($observaciones['items'] ?? [])->firstWhere('dotacion_id', $dotacion->id);
        $this->assertNotNull($item);
        $this->assertSame($motivo, $item['motivo']);
        $this->assertEquals(30, $item['faltante']);
        $this->assertEquals(30, $item['cantidad_entregada']);
    }
}
