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
                    <a href="{{ route('journals.import') }}" class="btn btn-success add-list my-1"><i class="fa-solid fa-file-import me-3"></i>Import</a>
                    <a href="{{ route('journals.export') }}" class="btn btn-warning add-list my-1"><i class="fa-solid fa-file-arrow-down me-3"></i>Export</a>
                    {{-- <a href="{{ route('journals.create') }}" class="btn btn-primary add-list my-1"><i class="fa-solid fa-plus me-3"></i>Add</a> --}}
                    <a href="{{ route('journals.index') }}" class="btn btn-danger add-list my-1"><i class="fa-solid fa-trash me-3"></i>Clear Search</a>
                </div>
            </div>
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
                {{-- <div class="col-lg-12 card-header mt-n4">
                    <form action="{{ route('journals.index') }}" method="GET">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">

                            <div class="form-group row align-items-center justify-content-between">
                                <label class="control-label col-sm-3" for="search">Search:</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="search" class="form-control me-1" name="search" placeholder="Search sale" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20 text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div> --}}

                @include('partials.journal-header')


                <hr>


                <div class="col-lg-12">
                    <div class="table-responsive">
                        <div class="overflow-auto" style="min-height:200px;max-height:400px">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                        {{-- <th scope="col">@sortablelink('user.name', 'User')</th> --}}
                                        <th scope="col">@sortablelink('journal_date', 'Date')</th>
                                        <th scope="col">@sortablelink('description', 'Description')</th>
                                        <th scope="col">@sortablelink('reference', 'Reference')</th>
                                        <th scope="col">@sortablelink('opening', 'Opening')</th>
                                        <th scope="col">
                                            @sortablelink('debit', 'Debit (+)')<br>
                                            ({{ number_format($totalDebit) }})
                                        </th>
                                        <th scope="col">
                                            @sortablelink('credit', 'Credit (-)')
                                            ({{ number_format($totalCredit) }})
                                        </th>
                                        <th scope="col">
                                            @sortablelink('due', 'Due')
                                            ({{ number_format($totalDue) }})
                                        </th>
                                        <th scope="col">@sortablelink('balance', 'Balance')</th>
                                        <th scope="col">@sortablelink('comment', 'Comment')</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($journals as $journal)
                                    <tr>
                                        <th scope="row">{{ (($journals->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                        {{-- <td>{{ $journal->user->name }}</td> --}}
                                        <td>{{ $journal->journal_date }}</td>
                                        <td>{{ $journal->description }}</td>
                                        <td>{{ $journal->reference }}</td>
                                        <td>{{ number_format($journal->opening) }}</td>
                                        <td>{{ number_format($journal->debit) }}</td>
                                        <td>{{ number_format($journal->credit) }}</td>
                                        <td>{{ number_format($journal->due) }}</td>
                                        <td>{{ number_format($journal->balance) }}</td>
                                        <td>{{ $journal->comment }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('journals.show', $journal->id) }}" class="btn btn-outline-success btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                                <a href="{{ route('journals.edit', $journal->id) }}" class="btn btn-outline-primary btn-sm mx-1"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('journals.destroy', $journal->id) }}" method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{ $journals->links() }}

                {{-- {{ $sales->links() }} --}}
            </div>

        </div>
    </div>
</div>
<!-- END: Main Page Content -->
@endsection
