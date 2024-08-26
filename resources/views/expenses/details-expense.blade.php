@extends('dashboard.body.main')

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
    <div class="container-xl px-4">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg></div>
                        Expense Details
                    </h1>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-4">
    <div class="row">

        <!-- BEGIN: Information -->
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    Information Expenses
                </div>
                <div class="card-body">
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (no expense) -->
                        <div class="col-md-4">
                            <label class="small mb-1">Expense No</label>
                            <div class="form-control form-control-solid">{{ $expense->expense_no }}</div>
                        </div>
                        <!-- Form Group (expense date) -->
                        <div class="col-md-4">
                            <label class="small mb-1">Expense Date</label>
                            <div class="form-control form-control-solid">{{ $expense->expense_date }}</div>
                        </div>
                        <!-- Form Group (paid amount) -->
                        <div class="col-md-4">
                            <label class="small mb-1">Total Amount</label>
                            <div class="form-control form-control-solid">{{ $expense->total_amount }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (comment) -->
                        <div class="col-md-4">
                            <label class="small mb-1" for="comment">Comment</label>
                            <textarea rows="3" class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" autocomplete="off">{{ $expense->comment }}</textarea>
                            @error('comment')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (due amount) -->
                        <div class="col-md-4">
                            <label class="small mb-1">Created By</label>
                            <div class="form-control form-control-solid">{{ $expense->user_created->name }}</div>
                        </div>
                        <!-- Form Group (paid amount) -->
                        <div class="col-md-4">
                            <label class="small mb-1">Updated By</label>
                            <div class="form-control form-control-solid">{{ $expense->user_updated ? $expense->user_updated->name : '-' }}</div>
                        </div>
                    </div>

                    @if ($expense->expense_status == 0)
                    <form action="{{ route('expenses.updateExpense') }}" method="POST">
                        @csrf
                        @method('put')
                        <input type="hidden" name="id" value="{{ $expense->id }}">
                        <!-- Submit button -->
                        @if (auth()->user()->role == 'admin')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this expense?')">Approve Expense</button>
                        @endif
                        <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
                    </form>
                    @else
                    <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
                    @endif
                </div>
            </div>
        </div>
        <!-- END: Information Supplier -->


        <!-- BEGIN: Table Issue -->
        <div class="col-xl-12">
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">List Issue</div>

                <div class="card-body">
                    <!-- BEGIN: Issues List -->
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Issue Name</th>
                                        <th scope="col">Issue Code</th>
                                        <th scope="col">Current Occurence</th>
                                        <th scope="col">Occurence</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenseDetails as $item)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration  }}</td>
                                        <td scope="row">{{ $item->issue->issue_name }}</td>
                                        <td scope="row">{{ $item->issue->issue_code }}</td>
                                        <td scope="row"><span class="btn btn-warning">{{ $item->issue->occurence }}</span></td>
                                        <td scope="row"><span class="btn btn-success">{{ $item->occurence }}</span></td>
                                        <td scope="row">{{ $item->unitcost }}</td>
                                        <td scope="row">
                                            <span  class="btn btn-primary">{{ $item->total }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END: Issues List -->
                </div>
            </div>
        </div>
        <!-- END: Table Issue -->
    </div>
</div>
<!-- END: Main Page Content -->
@endsection
