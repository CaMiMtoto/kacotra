<?php

namespace App\Exports;

use App\Models\OrderPayment;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class PaymentsExport implements FromQuery, WithMapping, WithColumnFormatting, ShouldAutoSize, WithHeadings, WithStyles, WithEvents
{
    use Exportable;

    private string $startDate;
    private string $endDate;
    private ?string $invoiceNo;

    // Variables to store totals
    private float $totalPaid = 0;
    private float $totalDue = 0;

    public function __construct(string $startDate, string $endDate, ?string $invoiceNo)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->invoiceNo = $invoiceNo;
    }

    public function query()
    {
        return OrderPayment::query()
            ->with(['order.customer', 'method'])
            ->when($this->startDate, function ($query) {
                $query->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('created_at', '<=', $this->endDate);
            })
            ->when($this->invoiceNo, function ($query) {
                $query->whereHas('order', function ($query) {
                    $query->where('invoice_no', $this->invoiceNo);
                });
            })
            ->latest();
    }

    public function headings(): array
    {
        return [
            'Payment Date',
            'Invoice No',
            'Customer Name',
            'Payment Method',
            'Amount Paid',
            'Amount Due',
        ];
    }

    public function map($row): array
    {
        // Accumulate totals
        $this->totalPaid += $row->pay;
        $this->totalDue += $row->due;

        return [
            $row->created_at->format('Y-m-d'),
            $row->order->invoice_no,
            $row->order->customer->name,
            $row->method->name,
            number_format($row->pay, 2),  // Format with two decimals
            number_format($row->due, 2)   // Format with two decimals
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Make the first row (headers) bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get the row number for totals, assuming data starts from row 2 (including headers)
                $lastRow = $sheet->getHighestRow() + 1;

                // Insert totals row at the end
                $sheet->setCellValue('D' . $lastRow, 'Total:');
                $sheet->setCellValue('E' . $lastRow, number_format($this->totalPaid, 2)); // Total Paid
                $sheet->setCellValue('F' . $lastRow, number_format($this->totalDue, 2));  // Total Due

                // Style the totals row (e.g., bold)
                $sheet->getStyle('D' . $lastRow . ':F' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }
}
