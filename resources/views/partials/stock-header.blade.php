<div class="col-md-12 card-header mt-n4">
    <div class="row">
        <div class="col-md-6">
            <form action="#" method="GET">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                    <div class="form-group row align-items-center">
                        <label for="row" class="col-auto">Row:</label>
                        <div class="col-auto">
                            <select class="form-control" name="row">
                                <option value="10" @if(request('row') == '10')selected="selected"@endif>10</option>
                                <option value="25" @if(request('row') == '25')selected="selected"@endif>25</option>
                                <option value="50" @if(request('row') == '50')selected="selected"@endif>50</option>
                                <option value="100" @if(request('row') == '100')selected="selected"@endif>100</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row align-items-center justify-content-between">
                        <div class="input-group">
                            <span class="input-group-text">Search:</span>
                            <input type="text" id="search" class="form-control" name="search" placeholder="Search order" value="{{ request('search') }}">
                            <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20 text-white"></i></button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="col-md-6">

            <form action="{{ route('stocks.filter') }}" method="get">
                <div class="input-group">
                    <span class="input-group-text">From</span>
                    <input type="date" name="from_date" aria-label="From" class="form-control" {{-- value="{{ $request->from_date }}" --}}>
                    <span class="input-group-text">To</span>
                    <input type="date" name="to_date" aria-label="To" class="form-control" {{-- value="{{ $request->to_date }}" --}}>
                    <button class="btn btn-primary" type="submit" id="button-addon1">Filter</button>
                </div>
            </form>

        </div>
    </div>
</div>
