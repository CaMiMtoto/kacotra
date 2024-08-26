@extends('dashboard.body.main')


@section('specificpagestyles')
<link href="{{ asset('assets/css/litepicker.css') }}" rel="stylesheet" />
@endsection

@section('specificpagescripts')
<script src="{{ asset('assets/js/chart.min.js') }}"></script>
<script src="{{ asset('assets/demo/chart-area-demo.js') }}"></script>
<script src="{{ asset('assets/demo/chart-bar-demo.js') }}"></script>
<script src="{{ asset('assets/js/litepicker.bundle.js') }}"></script>
<script src="{{ asset('assets/js/litepicker.js') }}"></script>
@endsection

@section('content')
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i data-feather="activity"></i></div>
                        Dashboard
                    </h1>
                    <div class="page-header-subtitle">KACOTRA Ltd Inventory Management System</div>
                </div>
                <div class="col-12 col-xl-auto mt-4">
                    <div class="input-group input-group-joined border-0" style="width: 16.5rem">
                        <span class="input-group-text"><i class="text-primary" data-feather="calendar"></i></span>
                        <input class="form-control ps-0 pointer" id="litepickerRangePlugin" placeholder="Select date range..." />
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main page content -->
<div class="container-xl px-4 mt-n10">
    <!-- Example Colored Cards for Dashboard Demo -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card bg-light text-white h-100">
                <div class="card-body">
                    <h1>{{ auth()->user()->name }} welcome to KACOTRA LTD</h1>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
