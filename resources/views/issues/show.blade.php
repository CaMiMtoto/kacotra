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
                        Details Issue
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('issues.index') }}">Issues</a></li>
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
            {{-- <div class="col-xl-4">
                <!-- Issue image card-->
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Issue Image</div>
                    <div class="card-body text-center">
                        <!-- Issue image -->
                        <img class="img-account-profile mb-2" src="{{ asset('assets/img/issues/default.webp') }}" alt="" id="image-preview" />
                    </div>
                </div>
            </div> --}}

            <div class="col-xl-8">
                <!-- BEGIN: Issue Code -->
                <div class="card mb-4">
                    <div class="card-header">
                        Issue Code
                    </div>
                    <div class="card-body">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (type of issue department) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Issue code</label>
                                <div class="form-control form-control-solid">{{ $issue->issue_code  }}</div>
                            </div>
                            <!-- Form Group (type of issue unit) -->
                            {{-- <div class="col-md-6 align-middle">
                                <label class="small mb-1">Barcode</label>
                                <div class="mt-1">
                                  {!! $barcode !!}
                                  </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <!-- END: Issue Code -->

                <!-- BEGIN: Issue Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        Issue Information
                    </div>
                    <div class="card-body">
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (issue name) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Issue name</label>
                                <div class="form-control form-control-solid">{{ $issue->issue_name }}</div>
                            </div>
                            <!-- Form Group (type of issue unit) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Issue unit</label>
                                <div class="form-control form-control-solid">{{ $issue->unit->name  }}</div>
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (buying price) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Buying price</label>
                                <div class="form-control form-control-solid">{{ $issue->cost  }}</div>
                            </div>
                            <!-- Form Group (type of issue department) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1">Issue department</label>
                                <div class="form-control form-control-solid">{{ $issue->department->name  }}</div>
                            </div> --}}
                            <!-- Form Group (occurence) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Occurence</label>
                                <div class="form-control form-control-solid">{{ $issue->occurence  }}</div>
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (selling price) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1">Selling price</label>
                                <div class="form-control form-control-solid">{{ $issue->selling_price  }}</div>
                            </div> --}}
                        </div>

                        <!-- Submit button -->
                        <a class="btn btn-primary" href="{{ route('issues.index') }}">Back</a>
                    </div>
                </div>
                <!-- END: Issue Information -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection
