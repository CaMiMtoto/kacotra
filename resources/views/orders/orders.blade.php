@extends('dashboard.body.main')

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto my-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-cash-register"></i></div>
                        Order List
                    </h1>
                </div>
                <div class="col-auto my-4">
                    <a href="{{ route('orders.getOrderReport') }}" class="btn btn-success add-list my-1"><i class="fa-solid fa-file-export me-3"></i>Export</a>
                    <a href="{{ route('orders.createOrder') }}" class="btn btn-primary add-list my-1"><i class="fa-solid fa-plus me-3"></i>Add</a>
                    <a href="{{ route('orders.allOrders') }}" class="btn btn-danger add-list my-1"><i class="fa-solid fa-trash me-3"></i>Clear Search</a>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">All Orders</li>
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
                                <div class="overflow-auto" style="min-height:200px;max-height:400px">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="thead-light">
                                {{-- <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">@sortablelink('customer.name', 'name')</th>
                                    <th scope="col">@sortablelink('order_date', 'Date')</th>
                                    <th scope="col">Payment</th>
                                    <th scope="col">@sortablelink('total')</th>
                                    <th scope="col">@sortablelink('pay', 'Paid')</th>
                                    <th scope="col">@sortablelink('due', 'Due')</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr> --}}
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Payment</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Paid</th>
                                    <th scope="col">Due</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach ($orders as $order)
                                    <tr>
                                        <th scope="row">{{ (($orders->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                        <td>{{ $order->invoice_no }}</td>
                                        <td>{{ $order->customer }}</td>
                                        <td>{{ $order->order_date }}</td>
                                        <td>{{ $order->payment_type }}</td>
                                        <td class="text-end">{{ number_format($order->total,0) }}</td>
                                        <td class="text-end">{{ number_format($order->pay,0) }}</td>
                                        <td class="text-end">{{ number_format($order->due,0) }}</td>
                                        <td>
                                            <span class="btn
                                            @if ($order->order_status == 'complete')
                                                btn-success
                                            @else
                                                btn-warning
                                            @endif  btn-sm text-uppercase">
                                            @if ($order->order_status == 'complete' && $order->due < 0)
                                            refund
                                            @else
                                            {{ $order->order_status }}
                                            @endif
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @if ($order->order_status == 'pending' && $order->is_confirmed == 1)
                                                <a href="{{ route('order.dueOrderDetails', $order->id) }}" class="btn btn-outline-warning btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                                @elseif ($order->order_status == 'complete' && $order->due < 0)
                                                <a href="{{ route('order.refundOrderDetails', $order->id) }}" class="btn btn-outline-danger btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                                @elseif ($order->order_status == 'refund' && $order->due < 0)
                                                <a href="{{ route('order.refundOrderDetails', $order->id) }}" class="btn btn-outline-danger btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                                {{-- @elseif ($order->is_deleted == 1)
                                                <a href="{{ route('order.deletedOrderDetails', $order->id) }}" class="btn btn-outline-danger btn-sm mx-1"><i class="fa-solid fa-eye"></i></a> --}}
                                                @elseif ($order->order_status == 'pending' && $order->is_confirmed == 0 && auth()->user()->role == 'seller')
                                                {{-- <form action="{{ route('order.deleteOrder', $order->id) }}" method="PUT">
                                                    @method('put')
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form> --}}
                                                <a href="{{ route('order.deleteOrder', $order->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')"><i class="far fa-trash-alt"></i></a>
                                                @else
                                                <a href="{{ route('order.orderCompleteDetails', $order->id) }}" class="btn btn-outline-success btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                                @endif
                                                <a href="{{ route('order.downloadInvoice', $order->id) }}" class="btn btn-outline-primary btn-sm mx-1"><i class="fa-solid fa-print"></i></a>
                                                {{-- <form action="{{ route('order.deleteOrder', $order->id) }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </form> --}}
                                            </div>

                                        </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>

                                </div>
                        <table class="table table-sm table-dark table-striped align-middle">
                            <tbody>
                                <tr>
                                    <th scope="row" colspan="6">Total Invoices</th>
                                    <td class="text-end">{{ number_format($totalInvoices,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Total Tax</th>
                                    <td class="text-end">{{ number_format($totalTax,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Total Paid in hand</th>
                                    <td class="text-end">{{ number_format($cashInHand,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Total Paid Mobile Money</th>
                                    <td class="text-end">{{ number_format($cashMoMo,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Total Paid with Cheque</th>
                                    <td class="text-end">{{ number_format($cashInCheque,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Cash Due</th>
                                    <td class="text-end">{{ number_format($cashDue,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Cash Over (to refund)</th>
                                    <td class="text-end">{{ number_format($cashOver,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Total Paid</th>
                                    <td class="text-end">{{ number_format($totalPaid,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6">Balance</th>
                                    <td class="text-end">{{ number_format($balance,0) }}</td>
                                    <td colspan="2"></td>
                                </tr>
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
