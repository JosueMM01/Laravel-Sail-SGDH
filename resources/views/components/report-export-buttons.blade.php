@props(['type', 'range'])

@php
    $params = ['range' => $range];
    if ($range === \App\Support\ReportDateRange::CUSTOM) {
        $params['from'] = request('from');
        $params['to'] = request('to');
    }
@endphp

<div class="flex items-center gap-2">
    <form method="GET" action="{{ route('reportes.pdf', $type) }}" class="contents">
        @foreach ($params as $name => $value)
            @if ($value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endif
        @endforeach
        <x-button variant="ghost" size="sm" title="Descargar PDF">
            <x-heroicon-o-document-text class="h-5 w-5" aria-hidden="true" />
            <span class="hidden sm:inline">PDF</span>
        </x-button>
    </form>
    <form method="GET" action="{{ route('reportes.excel', $type) }}" class="contents">
        @foreach ($params as $name => $value)
            @if ($value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endif
        @endforeach
        <x-button variant="secondary" size="sm" title="Descargar Excel">
            <x-heroicon-o-table-cells class="h-5 w-5" aria-hidden="true" />
            <span class="hidden sm:inline">Excel</span>
        </x-button>
    </form>
</div>
