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
                        Details Deposit
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('deposits.index') }}">Deposits</a></li>
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

            <div class="col-xl-12">
                <!-- BEGIN: Deposit Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        Deposit Information
                    </div>
                    <div class="card-body">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (type of deposit method) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Deposit code</label>
                                <div class="form-control form-control-solid">{{ $deposit->deposit_code  }}</div>
                            </div>
                            <!-- Form Group (deposit status) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Deposit status</label>
                                <span class="btn {{ $deposit->deposit_status ? 'btn-success' : 'btn-warning' }} btn-sm">{{ $deposit->deposit_status ? 'Approved' : 'Pending' }}</span>
                                {{-- <div class="form-control form-control-solid">{{ $deposit->deposit_status ? 'Approved' : 'Pending' }}</div> --}}
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (type of deposit method) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1">Deposit method</label>
                                <div class="form-control form-control-solid">{{ $deposit->method->name  }}</div>
                            </div> --}}
                            <!-- Form Group (type of deposit bank) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Deposit bank</label>
                                <div class="form-control form-control-solid">{{ $deposit->bank->name  }}</div>
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (buying price) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1">Account No</label>
                                <div class="form-control form-control-solid">{{ $deposit->account_no  }}</div>
                            </div> --}}
                            <!-- Form Group (selling price) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Amount</label>
                                <div class="form-control form-control-solid">{{ $deposit->amount  }}</div>
                            </div>
                        </div>
                        <!-- Form Group (stock) -->
                        {{-- <div class="mb-3">
                            <label class="small mb-1">Transaction ID</label>
                            <div class="form-control form-control-solid">{{ $deposit->transaction_id  }}</div>
                        </div> --}}

                        <!-- Submit button -->
                        <a class="btn btn-primary" href="{{ route('deposits.index') }}">Back</a>
                    </div>
                </div>
                <!-- END: Deposit Information -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection
