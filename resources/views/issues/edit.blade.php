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
                        Edit Issue
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('issues.index') }}">Issues</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
    <form action="{{ route('issues.update', $issue->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="row">
            {{-- <div class="col-xl-4">
                <!-- Issue image card-->
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Issue Image</div>
                    <div class="card-body text-center">
                        <!-- Issue image -->
                        <img class="img-account-profile mb-2" src="{{ $issue->issue_image ? asset('storage/issues/'.$issue->issue_image) : asset('assets/img/issues/default.webp') }}" alt="" id="image-preview" />
                        <!-- Issue image help block -->
                        <div class="small font-italic text-muted mb-2">JPG or PNG no larger than 2 MB</div>
                        <!-- Issue image input -->
                        <input class="form-control form-control-solid mb-2 @error('issue_image') is-invalid @enderror" type="file"  id="image" name="issue_image" accept="image/*" onchange="previewImage();">
                        @error('issue_image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div> --}}

            <div class="col-xl-8">
                <!-- BEGIN: Issue Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Issue Details
                    </div>
                    <div class="card-body">
                        <!-- Form Group (issue name) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="issue_name">Issue name <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('issue_name') is-invalid @enderror" id="issue_name" name="issue_name" type="text" placeholder="" value="{{ old('issue_name', $issue->issue_name) }}" autocomplete="off"/>
                            @error('issue_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (type of issue department) -->
                            <div class="col-md-6">
                                <label class="small mb-1" for="department_id">Issue department <span class="text-danger">*</span></label>
                                <select class="form-select form-control-solid @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                    <option selected="" disabled="">Select a department:</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @if(old('department_id', $issue->department_id) == $department->id) selected="selected" @endif>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (type of issue unit) -->
                            <div class="col-md-6">
                                <label class="small mb-1" for="unit_id">Unit <span class="text-danger">*</span></label>
                                <select class="form-select form-control-solid @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id">
                                    <option selected="" disabled="">Select a unit:</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" @if(old('unit_id', $issue->unit_id) == $unit->id) selected="selected" @endif>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (cost) -->
                            <div class="col-md-6">
                                <label class="small mb-1" for="cost">Cost <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('cost') is-invalid @enderror" id="cost" name="cost" type="text" placeholder="" value="{{ old('cost', $issue->cost) }}" autocomplete="off" />
                                @error('cost')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Form Group (selling price) -->
                            {{-- <div class="col-md-6">
                                <label class="small mb-1" for="selling_price">Selling price <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" type="text" placeholder="" value="{{ old('selling_price', $issue->selling_price) }}" autocomplete="off" />
                                @error('selling_price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div> --}}
                        </div>
                        <!-- Form Group (occurence) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="occurence">Occurence <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('occurence') is-invalid @enderror" id="occurence" name="occurence" type="text" placeholder="" value="{{ old('occurence', $issue->occurence) }}" autocomplete="off" />
                            @error('occurence')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Submit button -->
                        <button class="btn btn-primary" type="submit">Update</button>
                        <a class="btn btn-danger" href="{{ route('issues.index') }}">Cancel</a>
                    </div>
                </div>
                <!-- END: Issue Details -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->
@endsection
