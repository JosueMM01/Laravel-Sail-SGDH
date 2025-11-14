<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ViewReportExport implements FromView, ShouldAutoSize
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(private readonly string $view, private readonly array $data)
    {
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }
}
