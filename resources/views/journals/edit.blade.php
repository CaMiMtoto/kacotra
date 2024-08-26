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
                        Edit Journal
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('journals.index') }}">Journals</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
    <form action="{{ route('journals.update', $journal->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="row">

            <div class="col-xl-12">
                <!-- BEGIN: Journal Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Journal Details
                    </div>
                    <div class="card-body">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (journal name) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="journal_date">Date <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('journal_date') is-invalid @enderror" id="journal_date" name="journal_date" type="text" placeholder="" value="{{ old('journal_date', $journal->journal_name) }}"/>
                                @error('journal_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (description) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="description">Description <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('description') is-invalid @enderror" id="description" name="description" type="text" placeholder="" value="{{ old('description', $journal->description) }}" />
                                @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (reference) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="reference">Reference <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('reference') is-invalid @enderror" id="reference" name="reference" type="text" placeholder="" value="{{ old('reference', $journal->reference) }}" />
                                @error('reference')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (buying price) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="debit">Debit <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('debit') is-invalid @enderror" id="debit" name="debit" type="text" placeholder="" value="{{ old('debit', $journal->debit) }}" />
                                @error('debit')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (selling price) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="credit">Credit <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('credit') is-invalid @enderror" id="credit" name="credit" type="text" placeholder="" value="{{ old('credit', $journal->credit) }}" />
                                @error('credit')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (journal) -->
                            <div class="col-md-4">
                                <label class="small mb-1" for="balance">Balance <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('balance') is-invalid @enderror" id="balance" name="balance" type="text" placeholder="" value="{{ old('balance', $journal->balance) }}" />
                                @error('balance')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit button -->
                        <button class="btn btn-primary" type="submit">Update</button>
                        <a class="btn btn-danger" href="{{ route('journals.index') }}">Cancel</a>
                    </div>
                </div>
                <!-- END: Journal Details -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection
