@extends('dashboard.body.main')

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                        Details Stock
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stocks.index') }}">Stocks</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
        <div class="row">
            <div class="col-xl-4">
                <!-- Stock image card-->
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Stock Image</div>
                    <div class="card-body text-center">
                        <!-- Stock image -->
                        <img class="img-account-profile mb-2" src="{{ asset('assets/img/stocks/default.webp') }}" alt="" id="image-preview" />
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <!-- BEGIN: Stock Code -->
                <div class="card mb-4">
                    <div class="card-header">
                        Stock Code
                    </div>
                    <div class="card-body">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (type of stock category) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Stock code</label>
                                <div class="form-control form-control-solid">{{ $stock->stock_code  }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Stock Code -->

                <!-- BEGIN: Stock Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        Stock Information
                    </div>
                    <div class="card-body">
                        <!-- Form Group (stock name) -->
                        <div class="mb-3">
                            <label class="small mb-1">Stock name</label>
                            <div class="form-control form-control-solid">{{ $stock->stock_name }}</div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (type of stock category) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1">Stock category</label>
                                <div class="form-control form-control-solid">{{ $stock->category->name  }}</div>
                            </div> --}}
                            <!-- Form Group (type of stock unit) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1">Stock unit</label>
                                <div class="form-control form-control-solid">{{ $stock->unit->name  }}</div>
                            </div> --}}
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (buying price) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Buying price</label>
                                <div class="form-control form-control-solid">{{ $stock->buying_price  }}</div>
                            </div>
                            <!-- Form Group (selling price) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Selling price</label>
                                <div class="form-control form-control-solid">{{ $stock->selling_price  }}</div>
                            </div>
                        </div>
                        <!-- Form Group (stock) -->
                        <div class="mb-3">
                            <label class="small mb-1">Stock</label>
                            <div class="form-control form-control-solid">{{ $stock->stock  }}</div>
                        </div>

                        <!-- Submit button -->
                        <a class="btn btn-primary" href="{{ route('stocks.index') }}">Back</a>
                    </div>
                </div>
                <!-- END: Stock Information -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection
