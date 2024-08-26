@extends('dashboard.body.main')

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto my-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-clock"></i></div>
                        Complete Orders
                    </h1>
                </div>
                <div class="col-auto my-4">
                    <a href="{{ route('pos.index') }}" class="btn btn-primary add-list my-1"><i class="fa-solid fa-plus me-3"></i>Add</a>
                    <a href="{{ route('products.index') }}" class="btn btn-danger add-list my-1"><i class="fa-solid fa-trash me-3"></i>Clear Search</a>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Complete Orders</li>
                </ol>
            </nav>
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
                @include('partials.list-header')

                <hr>

                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">@sortablelink('customer.name', 'name')</th>
                                    <th scope="col">@sortablelink('order_date', 'Date')</th>
                                    <th scope="col">Payment</th>
                                    <th scope="col">@sortablelink('total')</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <th scope="row">{{ (($orders->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    <td>{{ $order->invoice_no }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->order_date }}</td>
                                    <td>{{ $order->payment_type }}</td>
                                    <td>{{ $order->total }}</td>
                                    <td>
                                        <span class="btn btn-success btn-sm text-uppercase">{{ $order->order_status }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @if($order->due > 0)
                                                <a href="{{ route('order.dueOrderDetails', $order->id) }}" class="btn btn-outline-warning btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                            @elseif($order->due < 0)
                                                <a href="{{ route('order.refundOrderDetails', $order->id) }}" class="btn btn-outline-secondary btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                            @else
                                                <a href="{{ route('order.orderCompleteDetails', $order->id) }}" class="btn btn-outline-success btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                            @endif

                                            <a href="{{ route('order.downloadInvoice', $order->id) }}" class="btn btn-outline-primary btn-sm mx-1"><i class="fa-solid fa-print"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
<!-- END: Main Page Content -->
@endsection
