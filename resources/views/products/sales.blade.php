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
                <div class="col-lg-12 card-header mt-n4">
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            {{-- <div class="form-group row align-items-center">
                                <label for="row" class="col-auto">Row:</label>
                                <div class="col-auto">
                                    <select class="form-control" name="row">
                                        <option value="10" @if(request('row') == '10')selected="selected"@endif>10</option>
                                        <option value="25" @if(request('row') == '25')selected="selected"@endif>25</option>
                                        <option value="50" @if(request('row') == '50')selected="selected"@endif>50</option>
                                        <option value="100" @if(request('row') == '100')selected="selected"@endif>100</option>
                                    </select>
                                </div>
                            </div> --}}

                            <div class="form-group row align-items-center justify-content-between">
                                <label class="control-label col-sm-3" for="search">Search:</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="search" class="form-control me-1" name="search" placeholder="Search product" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20 text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>

                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">No.</th>
                                    {{-- <th scope="col">Image</th> --}}
                                    <th scope="col">Product Name</th>
                                    {{-- <th scope="col">Category</th> --}}
                                    <th scope="col">Opening</th>
                                    <th scope="col">Buying</th>
                                    <th scope="col">Stock Value</th>
                                    <th scope="col">Quantity</th>
                                    {{-- <th scope="col">Unit</th> --}}
                                    <th scope="col">Sales</th>
                                    <th scope="col">Closing</th>
                                    <th scope="col">Closing Value</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                <tr>
                                    <th scope="row">{{ (($products->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                    {{-- <td>
                                        <div style="max-height: 80px; max-width: 80px;">
                                            <img class="img-fluid"  src="{{ $product->p_image ? asset('storage/products/'.$product->p_image) : asset('assets/img/products/default.webp') }}">
                                        </div>
                                    </td> --}}
                                    <td>{{ $product->p_name }}</td>
                                    {{-- <td>{{ $product->c_name }}</td> --}}
                                    <td class="text-center">{{ number_format($product->p_open) }}</td>
                                    <td class="text-center">{{ number_format($product->p_buying) }}</td>
                                    <td class="text-center">{{ number_format($product->p_stock) }}</td>
                                    <td class="text-center">{{ number_format($product->p_quantity) }}</td>
                                    {{-- <td>{{ $product->u_name }}</td> --}}
                                    <td class="text-end">{{ number_format($product->p_sales) }}</td>
                                    <td class="text-center">{{ number_format($product->p_close) }}</td>
                                    <td class="text-center">{{ number_format($product->p_close_value) }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('products.show', $product->p_id) }}" class="btn btn-outline-success btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                            {{-- <a href="{{ route('products.edit', $product->p_id) }}" class="btn btn-outline-primary btn-sm mx-1"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('products.destroy', $product->p_id) }}" method="POST">
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
                </div>

                {{-- {{ $products->links() }} --}}
            </div>
        </div>
    </div>
</div>
<!-- END: Main Page Content -->
@endsection
