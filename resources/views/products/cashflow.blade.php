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
                        {{ $title }}
                    </h1>
                </div>
                <div class="col-auto my-4">
                    <a href="{{ route('products.import') }}" class="btn btn-success add-list my-1"><i class="fa-solid fa-file-import me-3"></i>Import</a>
                    <a href="{{ route('products.export') }}" class="btn btn-warning add-list my-1"><i class="fa-solid fa-file-arrow-down me-3"></i>Export</a>
                    <a href="{{ route('products.create') }}" class="btn btn-primary add-list my-1"><i class="fa-solid fa-plus me-3"></i>Add</a>
                    <a href="{{ route('products.index') }}" class="btn btn-danger add-list my-1"><i class="fa-solid fa-trash me-3"></i>Clear Search</a>
                </div>
            </div>

            {{-- <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav> --}}
        </div>
    </div>

    <!-- BEGIN: Alert -->
    <div class="container-xl px-4 mt-n4">
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
    </div>
    <!-- END: Alert -->
</header>
<!-- END: Header -->


<!-- BEGIN: Main Page Content -->
<div class="container px-2 mt-n10">
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mx-n4">
                {{-- <div class="col-lg-12 card-header mt-n4">
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">

                            <div class="form-group row align-items-center justify-content-between">
                                <label class="control-label col-sm-3" for="search">Search:</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="search" class="form-control me-1" name="search" placeholder="Search sale" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20 text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div> --}}

                @include('partials.cashflow-header')

                <hr>

                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">No.</th>
                                    {{-- <th scope="col">Image</th> --}}
                                    <th scope="col">Description</th>
                                    <th scope="col">Details</th>
                                    <th scope="col">Amount</th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="4">Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                <tr>
                                    <th scope="row">{{ (($sales->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $sale->p_name }}</td>
                                    <td class="text-center">Qty: {{ number_format($sale->p_quantity) }}</td>
                                    <td class="text-end">{{ number_format($sale->p_sales) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th scope="row" colspan="3">Total Paid</th>
                                    <td class="text-end text-success">{{ number_format($total_paid) }} RwF</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="3">Total Due</th>
                                    <td class="text-end text-danger">{{ number_format($total_due) }} RwF</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="3">Total Excess</th>
                                    <td class="text-end text-primary">{{ number_format($total_refund) }} RwF</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="3">Sub Total 1 (Sales value)</th>
                                    <td class="text-end">{{ number_format($subTotal1) }} RwF</td>
                                </tr>

                            </tbody>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" colspan="4">Purchases</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                <tr>
                                    <th scope="row">{{ (($purchases->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $purchase->pur_name}}</td>
                                    <td class="text-center">Qty: {{ number_format($purchase->pur_quantity) }}</td>
                                    <td class="text-end">{{ number_format($purchase->pur_total) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th scope="row" colspan="3">Sub Total 2</th>
                                    <td class="text-end">{{ number_format($subTotal2) }} RwF</td>
                                </tr>

                            </tbody>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" colspan="4">Expenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                <tr>
                                    <th scope="row">{{ (($expenses->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $expense->exp_name}}</td>
                                    <td class="text-center">Freq: {{ number_format($expense->exp_quantity) }}</td>
                                    <td class="text-end">{{ number_format($expense->exp_total) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th scope="row" colspan="3">Sub Total 3</th>
                                    <td class="text-end">{{ number_format($subTotal3) }} RwF</td>
                                </tr>

                            </tbody>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" colspan="4">Deposits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deposits as $deposit)
                                <tr>
                                    <th scope="row">{{ (($deposits->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $deposit->dep_code}}</td>
                                    <td class="text-center">Bank: {{ $deposit->dep_name }}</td>
                                    <td class="text-end">{{ number_format($deposit->dep_total) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th scope="row" colspan="3">Sub Total 4</th>
                                    <td class="text-end">{{ number_format($subTotal4) }} RwF</td>
                                </tr>

                            </tbody>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" colspan="4">Damages</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($damages as $damage)
                                <tr>
                                    <th scope="row">{{ (($damages->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $damage->dam_name}}</td>
                                    <td class="text-center">Qty: {{ number_format($damage->dam_quantity) }}</td>
                                    <td class="text-end">{{ number_format($damage->dam_total) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr class="bg-warning">
                                    <th scope="row" colspan="3">Sub Total 5</th>
                                    <td class="text-end">{{ number_format($subTotal5) }} RwF</td>
                                </tr>

                            </tbody>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" colspan="4">Recoveries</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recoveries as $recovery)
                                <tr>
                                    <th scope="row">{{ (($recoveries->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $recovery->rec_invoice }} <span class="text-sm fw-lighter fst-italic">({{ $recovery->rec_customer }})</span></td>
                                    <td class="text-center">Method: {{ $recovery->rec_pay_type }}</td>
                                    <td class="text-end">{{ number_format($recovery->rec_total) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th scope="row" colspan="3">Sub Total 6</th>
                                    <td class="text-end">{{ number_format($subTotal6) }} RwF</td>
                                </tr>

                            </tbody>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" colspan="4">Refunds</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($refunds as $refund)
                                <tr>
                                    <th scope="row">{{ (($refunds->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $refund->ref_invoice}} <span class="text-sm fw-lighter fst-italic">({{ $refund->ref_customer }})</span></td>
                                    <td class="text-center">Method: {{ $refund->ref_pay_type }}</td>
                                    <td class="text-end">{{ number_format($refund->ref_total) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th scope="row" colspan="3">Sub Total 7</th>
                                    <td class="text-end">{{ number_format($subTotal7) }} RwF</td>
                                </tr>
                            </tbody>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" colspan="4">Dues</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dues as $due)
                                <tr>
                                    <th scope="row">{{ (($dues->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $due->due_invoice }} <span class="text-sm fw-lighter fst-italic">({{ $due->due_customer ?? null }})</span></td>
                                    <td class="text-center">{{ $due->due_comment }} </td>
                                    <td class="text-end">{{ number_format($due->due_total) }} RwF</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th scope="row" colspan="3">Sub Total 8</th>
                                    <td class="text-end">{{ number_format($subTotal8) }} RwF</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="3">Balance <span class="text-sm fw-lighter fst-italic">(Sales (Total paid) - (Expenses + Deposits + Refunds) + Recoveries)</span></th>
                                    <td class="text-end fw-bolder @if ($total < 0) text-danger @endif">{{ number_format($total) }} RwF</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- {{ $sales->links() }} --}}
            </div>

        </div>
    </div>
</div>
<!-- END: Main Page Content -->
@endsection
