@extends('dashboard.body.main')

@section('content')
    <!-- BEGIN: Header -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto my-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                            Payment Report
                        </h1>
                    </div>
                    <div class="col-auto my-4">
                        <a href="{{ route('products.import') }}" class="btn btn-success add-list my-1"><i
                                class="fa-solid fa-file-import me-3"></i>Import</a>
                        <a href="{{ route('orders.payments.export',['start_date' => $startDate,'end_date' => $endDate,'invoice_no' => $invoiceNo]) }}"
                           class="btn btn-warning add-list my-1">
                            <i class="fa-solid fa-file-arrow-down me-3"></i>Export
                        </a>
                        <a href="{{ route('orders.payments.report') }}" class="btn btn-danger add-list my-1"><i
                                class="fa-solid fa-trash me-3"></i>Clear Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- BEGIN: Alert -->
        <div class="container-xl mt-n4">
            @if (session()->has('success'))
                <div class="alert alert-success alert-icon" role="alert">
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div class="alert-icon-aside">
                        <i class="far fa-flag"></i>
                    </div>
                    <div class="alert-icon-content">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <div class="my-4 card card-body">
                <h4>Filters</h4>
                <p class="text-muted small">
                    You can filter the report by date range and search for a specific order by typing the invoice number
                    in the search box.
                </p>
                <form action="{{ route('orders.payments.report') }}" method="get">
                    <div class="input-group">
                        <span class="input-group-text">Invoice #</span>
                        <input type="text" name="invoice_no" aria-label="invoice_no"
                               placeholder="Invoice number.."
                               class="form-control" value="{{ request('invoice_no') }}"/>
                        <span class="input-group-text">From</span>
                        <input type="date" name="start_date" aria-label="Stat Date" class="form-control"
                               value="{{ request('start_date') }}">
                        <span class="input-group-text">To</span>
                        <input type="date" name="end_date" aria-label="End Date" class="form-control"
                               value="{{ request('end_date') }}">
                        <button class="btn btn-primary" type="submit" id="button-addon1">Filter</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- END: Alert -->


    </header>
    <!-- END: Header -->



    <div class="container px-2 mt-n10">
        <div class="card mb-4">
            <div class="card-body">
                <h4>
                    Payment Report
                    <small>From {{ $startDate }} To {{ $endDate }}</small>

                    @if($invoiceNo)
                        <small>For Invoice # {{ $invoiceNo }}</small>
                    @endif

                </h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Payment Type</th>
                            <th>Amount Paid</th>
                            <th>Amount Due</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="3">Total</td>
                            <td></td>
                            <td>{{ number_format($payments->sum('pay')) }}</td>
                            <td>{{ number_format($payments->sum('due')) }}</td>
                        </tfoot>
                        <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                <td>{{ $payment->order->invoice_no }}</td>
                                <td>{{ $payment->order->customer->name }}</td>
                                <td>{{ $payment->method->name }}</td>
                                <td>{{ number_format($payment->pay) }}</td>
                                <td>{{ number_format($payment->due) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="">
                        {{ $payments->links() }}
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection
