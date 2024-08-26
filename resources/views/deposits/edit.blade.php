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
                        Edit Deposit
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('deposits.index') }}">Deposits</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
    <form action="{{ route('deposits.update', $deposit->id) }}" method="POST">
        @csrf
        @method('put')
        <div class="row">
            <div class="col-xl-12">
                <!-- BEGIN: Deposit Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Deposit Details
                    </div>
                    <div class="card-body">
                        <!-- Form Group (deposit name) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="deposit_date">Deposit date <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('deposit_date') is-invalid @enderror" id="deposit_date" name="deposit_date" type="text" placeholder="" value="{{ old('deposit_date', $deposit->deposit_date) }}" autocomplete="off"/>
                            @error('deposit_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (deposit name) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="deposit_status">Deposit status <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('deposit_status') is-invalid @enderror" id="deposit_status" name="deposit_status" type="text" placeholder="" value="{{ old('deposit_status', $deposit->deposit_status) }}" autocomplete="off"/>
                            @error('deposit_status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input @error('deposit_status') is-invalid @enderror" type="checkbox" role="switch" name="deposit_status" id="deposit_status" checked>
                            <label class="form-check-label" for="deposit_status">Checked switch checkbox input</label>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (type of deposit method) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1" for="method_id">Deposit method <span class="text-danger">*</span></label>
                                <select class="form-select form-control-solid @error('method_id') is-invalid @enderror" id="method_id" name="method_id">
                                    <option selected="" disabled="">Select a method:</option>
                                    @foreach ($methods as $method)
                                    <option value="{{ $method->id }}" @if(old('method_id', $deposit->method_id) == $method->id) selected="selected" @endif>{{ $method->name }}</option>
                                    @endforeach
                                </select>
                                @error('method_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div> --}}
                            <!-- Form Group (type of deposit bank) -->
                            <div class="col-md-6">
                                <label class="small mb-1" for="bank_id">Bank <span class="text-danger">*</span></label>
                                <select class="form-select form-control-solid @error('bank_id') is-invalid @enderror" id="bank_id" name="bank_id">
                                    <option selected="" disabled="">Select a bank:</option>
                                    @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" @if(old('bank_id', $deposit->bank_id) == $bank->id) selected="selected" @endif>{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (buying price) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1" for="account_no">Account No <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('account_no') is-invalid @enderror" id="account_no" name="account_no" type="number" placeholder="" value="{{ old('account_no', $deposit->account_no) }}" autocomplete="off" />
                                @error('account_no')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div> --}}
                            <!-- Form Group (selling price) -->
                            <div class="col-md-6">
                                <label class="small mb-1" for="amount">Amount <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('amount') is-invalid @enderror" id="amount" name="amount" type="text" placeholder="" value="{{ old('amount', $deposit->amount) }}" autocomplete="off" />
                                @error('amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- Form Group (transaction_id) -->
                        {{-- <div class="mb-3">
                            <label class="small mb-1" for="transaction_id">Transaction ID <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('transaction_id') is-invalid @enderror" id="transaction_id" name="transaction_id" type="text" placeholder="" value="{{ old('transaction_id', $deposit->transaction_id) }}" autocomplete="off" />
                            @error('transaction_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div> --}}

                        <!-- Submit button -->
                        <button class="btn btn-primary" type="submit">Update</button>
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
