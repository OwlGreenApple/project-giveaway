@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Event : <span class="main-color">{{ $ev->title }}</span></h3>
                </div>

                <div class="card-body">
                    <table id="contestant" class="cell-border" border="0">
                        <thead>
                            <th>Name</th>
                            <th>Email</th>
                            <th>WA Number</th>
                            <th>Entries</th>
                            <th>Referrals</th>
                            <th>Date Enter</th>
                            <th>IP Address</th>
                        </thead>
                        <tbody>
                            @if($data->count() > 0)
                                @foreach($data as $row)
                                <tr>
                                    <td class="align-middle"><span class="main-color">{{ $row->c_name }}</span><br/>{{ $row->award }}</td>
                                    <td class="align-middle">{{ $row->c_email }}</td>
                                    <td class="align-middle">{{ $row->wa_number }}</td>
                                    <td class="align-middle">{{ $row->entries }}</td>
                                    <td class="align-middle">{{ $row->referrals }}</td>
                                    <td class="align-middle">{{ $row->date_enter }}</td>
                                    <td class="align-middle">{{ $row->ip }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="4" class="text-center"><div class="alert alert-info">{{ Lang::get('custom.no_data') }}</div></tr> 
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        data_table();
    });

    function data_table()
    {
        $("#contestant").DataTable();
    }
</script>
@endsection
