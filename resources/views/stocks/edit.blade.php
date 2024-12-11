@extends('dashboard.body.main')

@section('specificpagescripts')
    <script src="{{ asset('assets/js/img-preview.js') }}"></script>
@endsection

@section('content')
    <!-- BEGIN: Header -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                            Edit Stock
                        </h1>
                    </div>
                </div>

                <nav class="mt-4 rounded" aria-label="breadcrumb">
                    <ol class="breadcrumb px-3 py-2 rounded mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('stocks.index') }}">Stocks</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <!-- END: Header -->

    <!-- BEGIN: Main Page Content -->
    <div class="container-xl px-2 mt-n10">
        <form action="{{ route('stocks.update', $stock->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="row">
                <div class="col-xl-4">
                    <!-- Stock image card-->
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header">
                            Product Details
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <img id="preview" src="{{ $stock->product->image_url }}" alt="Image"
                                     class="img-fluid rounded" width="200" height="200"/>
                            </div>
                            <p>
                                <strong>Name:</strong> {{ $stock->product->product_name }}
                            </p>
                            <p>
                                <strong>Code:</strong> {{ $stock->product->product_code }}
                            </p>
                            <p>
                                <strong>Stock:</strong> {{ number_format($stock->product->stock) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <!-- BEGIN: Stock Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            Stock Details
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <h4 class="alert-heading">Errors:</h4>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="reference">Stock Reference</label>
                                        <input class="form-control" id="reference" name="reference" type="text"
                                               value="{{ $stock->reference }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="stock_date">Stock Date</label>
                                        <input class="form-control" id="stock_date" name="stock_date" type="date"
                                               value="{{ $stock->stock_date }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="opening">Opening Stock</label>
                                        <input class="form-control" id="opening" name="opening" type="number"
                                               value="{{ $stock->opening }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="closing">Closing Stock</label>
                                        <input class="form-control" id="closing" name="closing" type="number"
                                               value="{{ $stock->closing }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="sales">Sales</label>
                                        <input class="form-control" id="sales" name="sales" type="number"
                                               value="{{ $stock->sales }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="purchases">Purchases</label>
                                        <input class="form-control" id="purchases" name="purchases" type="number"
                                               value="{{ $stock->purchases }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="damages">Damages</label>
                                        <input class="form-control" id="damages" name="damages" type="number"
                                               value="{{ $stock->damages }}" disabled/>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit button -->
                            <button class="btn btn-primary" type="submit">Update</button>
                            <a class="btn btn-danger" href="{{ route('stocks.index') }}">Cancel</a>
                        </div>
                    </div>
                    <!-- END: Stock Details -->
                </div>
            </div>
        </form>
    </div>
    <!-- END: Main Page Content -->
@endsection
