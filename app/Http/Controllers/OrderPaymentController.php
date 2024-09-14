<?php

namespace App\Http\Controllers;

use App\Exports\PaymentsExport;
use App\Models\OrderPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderPaymentController extends Controller
{
    public function report()
    {
        $startDate = \request('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = \request('end_date', Carbon::now()->format('Y-m-d'));
        $invoiceNo = \request('invoice_no');

        $payments = OrderPayment::query()
            ->with(['order', 'method'])
            ->when($startDate, function (Builder $query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function (Builder $query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($invoiceNo, function (Builder $query) {
                $query->whereHas('order', function (Builder $query) {
                    $query->where('invoice_no', \request('invoice_no'));
                });
            })
            ->latest()
            ->paginate(20)
            ->appends(\request()->all());

        return view('order-payment.report', compact('payments', 'startDate', 'endDate', 'invoiceNo'));
    }

    public function exportExcel()
    {
        $startDate = \request('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = \request('end_date', Carbon::now()->format('Y-m-d'));
        $invoiceNo = \request('invoice_no');

        return (new PaymentsExport($startDate, $endDate, $invoiceNo))
            ->download("payments-{$startDate}-{$endDate}-{$invoiceNo}.xlsx");
    }
}
