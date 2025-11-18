<?php

namespace App\Http\Controllers;

use App\Exports\ViewReportExport;
use App\Http\Requests\ReportDownloadRequest;
use App\Services\Reports\InventoryReportService;
use App\Support\ReportDateRange;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private readonly InventoryReportService $reports)
    {
    }

    public function index(Request $request)
    {
        $rangeKey = $request->input('range', ReportDateRange::LAST_7_DAYS);
        $range = ReportDateRange::resolve($rangeKey, $request->input('from'), $request->input('to'));

        $areaConsumption = $this->reports->areaConsumption($range->start, $range->end);
        $productConsumption = $this->reports->productConsumption($range->start, $range->end);
        $solicitudes = $this->reports->solicitudesSummary($range->start, $range->end);
        $entregas = $this->reports->entregas($range->start, $range->end);
        $stats = $this->reports->globalStats($range->start, $range->end);

        return view('reportes.index', [
            'rangeOptions' => ReportDateRange::options(),
            'range' => $range,
            'selectedRange' => $rangeKey,
            'areaConsumption' => $areaConsumption,
            'productConsumption' => $productConsumption,
            'solicitudesSummary' => $solicitudes,
            'entregas' => $entregas,
            'stats' => $stats,
            'reportDefinitions' => $this->definitions(),
        ]);
    }

    public function downloadPdf(ReportDownloadRequest $request, string $type)
    {
        $definition = $this->definitions()[$type] ?? null;

        abort_unless($definition, 404);

        $range = $request->range();
        $data = $this->payloadFor($type, $range);

        $pdf = Pdf::loadView($definition['pdf_view'], $data);

        return $pdf->download($this->buildFilename($type, $range, 'pdf'));
    }

    public function downloadExcel(ReportDownloadRequest $request, string $type)
    {
        $definition = $this->definitions()[$type] ?? null;

        abort_unless($definition, 404);

        $range = $request->range();
        $data = $this->payloadFor($type, $range);

        return Excel::download(new ViewReportExport($definition['excel_view'], $data), $this->buildFilename($type, $range, 'xlsx'));
    }

    private function payloadFor(string $type, ReportDateRange $range): array
    {
        return match ($type) {
            'consumo-areas' => [
                'title' => 'Reporte de consumo por área',
                'range' => $range,
                'records' => $this->reports->areaConsumption($range->start, $range->end),
                'stats' => $this->reports->globalStats($range->start, $range->end),
            ],
            'consumo-productos' => [
                'title' => 'Reporte de consumo por producto',
                'range' => $range,
                'records' => $this->reports->productConsumption($range->start, $range->end),
                'stats' => $this->reports->globalStats($range->start, $range->end),
            ],
            'solicitudes' => [
                'title' => 'Reporte de solicitudes extraordinarias',
                'range' => $range,
                'summary' => $this->reports->solicitudesSummary($range->start, $range->end),
            ],
            'entregas' => [
                'title' => 'Reporte de entregas realizadas',
                'range' => $range,
                'records' => $this->reports->entregas($range->start, $range->end),
                'stats' => $this->reports->globalStats($range->start, $range->end),
            ],
            default => abort(404),
        };
    }

    private function definitions(): array
    {
        return [
            'consumo-areas' => [
                'title' => 'Consumo por área',
                'description' => 'Unidades entregadas por área del hospital, útil para evaluar dotaciones y consumos extraordinarios.',
                'pdf_view' => 'reportes.pdf.consumo-areas',
                'excel_view' => 'reportes.excel.consumo-areas',
            ],
            'consumo-productos' => [
                'title' => 'Consumo por producto',
                'description' => 'Productos con mayor rotación y distribución por áreas.',
                'pdf_view' => 'reportes.pdf.consumo-productos',
                'excel_view' => 'reportes.excel.consumo-productos',
            ],
            'solicitudes' => [
                'title' => 'Solicitudes extraordinarias',
                'description' => 'Seguimiento de solicitudes por estado y área solicitante.',
                'pdf_view' => 'reportes.pdf.solicitudes',
                'excel_view' => 'reportes.excel.solicitudes',
            ],
            'entregas' => [
                'title' => 'Entregas registradas',
                'description' => 'Historial de entregas con detalle de responsables y cantidades surtidas.',
                'pdf_view' => 'reportes.pdf.entregas',
                'excel_view' => 'reportes.excel.entregas',
            ],
        ];
    }

    private function buildFilename(string $type, ReportDateRange $range, string $extension): string
    {
        return Str::of($type)
            ->slug('-')
            ->append('_', $range->start->format('Ymd'), '_', $range->end->format('Ymd'), '.', $extension)
            ->value();
    }
}
