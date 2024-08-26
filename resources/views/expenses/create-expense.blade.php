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
                        Add Expense
                    </h1>
                </div>
            </div>

            <nav class="mt-4 rounded" aria-label="breadcrumb">
                <ol class="breadcrumb px-3 py-2 rounded mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('issues.index') }}">Expenses</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-2 mt-n10">
    <form action="{{ route('expenses.storeExpense') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xl-4">
                <!-- BEGIN: Expense Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Expense Details
                    </div>
                    <div class="card-body">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (date) -->
                            <div class="col-md-12">
                                <label class="small my-1" for="expense_date">Date <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid example-date-input @error('expense_date') is-invalid @enderror" name="expense_date" id="date" type="date" value="{{ old('expense_date') }}" required>
                                @error('expense_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Form Group (comment) -->
                            <div class="col-md-12">
                                <label class="small mb-1" for="comment">Comment</label>
                                <textarea rows="3" class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" autocomplete="off">{{ old('comment') }}</textarea>
                                @error('comment')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Expense Details -->
            </div>

            <div class="col-xl-8">
                <!-- BEGIN: Issue List -->
                <div class="card mb-4">
                    <div class="card-header">
                        Issue Details
                    </div>
                    <div class="card-body">
                    <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (issue) -->
                            <div class="col-md-10">
                                <label class="small my-1" for="issue_id">Issue <span class="text-danger">*</span></label>
                                <select class="form-select form-control-solid @error('issue_id') is-invalid @enderror" id="issue_id" name="issue_id">
                                    <option selected="" disabled="">Select an issue:</option>
                                    @foreach ($issues as $issue)
                                    <option value="{{ $issue->id }}" @if(old('issue_id') == $issue->id) selected="selected" @endif>{{ $issue->issue_name }}</option>
                                    @endforeach
                                </select>
                                @error('issue_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>



                            <div class="col-md-2">
                                <label class="small my-1"> </label>
                                <button class="btn btn-primary form-control addEventMore" type="button">Add Issue</button>
                            </div>
                        </div>

                        <div class="gx-3 table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody id="addRow" class="addRow">

                                </tbody>

                                <tbody>
                                    <tr class="table-primary">
                                        <td colspan="3"></td>
                                        <td>
                                            <input type="text" name="total_amount" value="0" id="total_amount" class="form-control total_amount" readonly>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-outline-success" onclick="return confirm('Are you sure you want to expense?')">
                                                Expense Save
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- END: Issue List -->
            </div>
        </div>
    </form>
</div>
<!-- END: Main Page Content -->

@endsection

@section('specificpagescripts')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/handlebars.js') }}"></script>
<script src="{{ asset('assets/js/notify.min.js') }}" ></script>

<script id="document-template" type="text/x-handlebars-template">
    <tr class="delete_add_more_item" id="delete_add_more_item">
        <td>
            <input type="hidden" name="issue_id[]" value="@{{issue_id}}" required>
            @{{issue_name}}
        </td>

        <td>
            <input type="number" min="1" class="form-control occurence" name="occurence[]" value="" required>
        </td>

        <td>
            <input type="number" class="form-control unitcost" name="unitcost[]" value="" required>
        </td>

        <td>
            <input type="number" class="form-control total" name="total[]" value="0" readonly>
        </td>

        <td>
            <button class="btn btn-danger removeEventMore" type="button">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click",".addEventMore", function() {
            var issue_id = $('#issue_id').val();
            var issue_name = $('#issue_id').find('option:selected').text();

            if(issue_id == ''){
                $.notify("Issue Field is Required" ,  {globalPosition: 'top right', className:'error' });
                return false;
            }

            var source = $("#document-template").html();
            var tamplate = Handlebars.compile(source);
            var data = {
                issue_id:issue_id,
                issue_name:issue_name

            };
            var html = tamplate(data);
            $("#addRow").append(html);
        });

        $(document).on("click",".removeEventMore",function(event){
            $(this).closest(".delete_add_more_item").remove();
            totalAmountPrice();
        });

        $(document).on('keyup click','.unitcost,.occurence', function(){
            var unitcost = $(this).closest("tr").find("input.unitcost").val();
            var occurence = $(this).closest("tr").find("input.occurence").val();
            var total = unitcost * occurence;
            $(this).closest("tr").find("input.total").val(total);
            totalAmountPrice();
        });


        // Calculate sum of amount in invoice
        function totalAmountPrice(){
            var sum = 0;
            $(".total").each(function(){
                var value = $(this).val();
                if(!isNaN(value) && value.length != 0){
                    sum += parseFloat(value);
                }
            });
            $('#total_amount').val(sum);
        }
    });
</script>

<!-- Get Issues by department -->
<script type="text/javascript">
    $(function(){
        $(document).on('change','#department_id',function(){
            var department_id = $(this).val();
            $.ajax({
                url:"{{ route('get-all-issue') }}",
                type: "GET",
                data:{department_id:department_id},
                success:function(data){
                    var html = '';
                    $.each(data,function(key,v){
                        html += '<option value=" '+v.id+' "> '+v.issue_name+'</option>';
                    });
                    $('#issue_id').html(html);
                }
            })
        });
    });

</script>
@endsection
