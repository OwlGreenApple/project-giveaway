@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Daftar User</span></h3>
                </div>

                <div class="card-body">
                    <table id="contestant" class="cell-border" border="0">
                        <thead>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Membership</th>
                            <th>Akhir Membership</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @if($users->count() > 0)
                                @foreach($users as $row)
                                <tr>
                                    <td class="align-middle"><span class="main-color">{{ $no++ }}</span></td>
                                    <td class="align-middle">{{ $row->name }}</td>
                                    <td class="align-middle">{{ $row->email }}</td>
                                    <td class="align-middle">{{ $row->membership }}</td>
                                    <td class="align-middle">{{ $row->end_membership }}</td>
                                    <td class="align-middle">@if($row->status > 0) <button type="button" class="btn btn-danger">Ban</button> @else <span class="text-danger">Banned</span> @endif</td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="6" class="text-center"><div class="alert alert-info mb-0">{{ Lang::get('custom.no_data') }}</div></tr> 
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
