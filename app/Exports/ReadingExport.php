<?php

namespace App\Exports;

use App\Models\Device;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class ReadingExport implements FromCollection, WithHeadings
{
    protected $rows;
    protected array $headings;

    public function __construct($rows, array $headings)
    {
        $this->rows = $rows;
        $this->headings = $headings;
    }
    public function collection(): Collection
    {
        return $this->rows;
    }

    #[Override]
    public function headings(): array
    {
        return $this->headings;
    }
}
