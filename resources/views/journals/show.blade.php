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
                        Details Journal
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('journals.index') }}">Journals</a></li>
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

                <!-- BEGIN: Journal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        Journal Information
                    </div>
                    <div class="card-body">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (journal description) -->
                            <div class="col-md-9">
                                <label class="small mb-1">Description</label>
                                <div class="form-control form-control-solid">{{ $journal->description }}</div>
                            </div>
                            <!-- Form Group (buying price) -->
                            <div class="col-md-3">
                                <label class="small mb-1">Reference</label>
                                <div class="form-control form-control-solid">{{ $journal->reference  }}</div>
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (journal name) -->
                            <div class="col-md-3">
                                <label class="small mb-1">Journal date</label>
                                <div class="form-control form-control-solid">{{ $journal->journal_date }}</div>
                            </div>
                            <!-- Form Group (buying price) -->
                            <div class="col-md-3">
                                <label class="small mb-1">Debit</label>
                                <div class="form-control form-control-solid">{{ $journal->debit  }}</div>
                            </div>
                            <!-- Form Group (selling price) -->
                            <div class="col-md-3">
                                <label class="small mb-1">Credit</label>
                                <div class="form-control form-control-solid">{{ $journal->credit  }}</div>
                            </div>
                            <!-- Form Group (journal) -->
                            <div class="col-md-3">
                                <label class="small mb-1">Balance</label>
                                <div class="form-control form-control-solid">{{ $journal->balance  }}</div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <a class="btn btn-primary" href="{{ route('journals.index') }}">Back</a>
                    </div>
                </div>
                <!-- END: Journal Information -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection
