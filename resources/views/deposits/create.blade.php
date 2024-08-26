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
                        Add Deposit
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('deposits.index') }}">Deposits</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
    <form action="{{ route('deposits.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="col-xl-12">
                <!-- BEGIN: Deposit Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Deposit Details
                    </div>
                    <div class="card-body">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (deposit date) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="deposit_date">Deposit date <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('deposit_date') is-invalid @enderror" id="deposit_date" name="deposit_date" type="date" placeholder="" value="{{ old('deposit_date') }}" autocomplete="off"/>
                                @error('deposit_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (type of deposit bank) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="bank_id">Bank <span class="text-danger">*</span></label>
                                <select class="form-select form-control-solid @error('bank_id') is-invalid @enderror" id="bank_id" name="bank_id">
                                    <option selected="" disabled="">Select a bank:</option>
                                    @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" @if(old('bank_id') == $bank->id) selected="selected" @endif>{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (amount) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="amount">Amount <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('amount') is-invalid @enderror" id="amount" name="amount" type="number" placeholder="" value="{{ old('amount') }}" autocomplete="off" />
                                @error('amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- Submit button -->
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a class="btn btn-danger" href="{{ route('deposits.index') }}">Cancel</a>
                    </div>
                </div>
                <!-- END: Deposit Details -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection
