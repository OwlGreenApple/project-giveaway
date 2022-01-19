@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Your Giveaways</h3>
                    <div class="float-end align-middle "><a href="{{ url('create') }}" class="btn btn-default bg-custom text-white">New Giveaway</a></div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Titles</th>
                            <th>Contestants</th>
                            <th>Entries</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @if($data->count() > 0)
                                @foreach($data as $row)
                                <tr>
                                    <td class="align-middle"><span class="main-color">{{ $row->title }}</span><br/>{{ $row->award }}</td>
                                    <td class="align-middle">5</td>
                                    <td class="align-middle">14</td>
                                    <td class="align-middle"><span class="badge rounded-pill bg-custom">Running</span></td>
                                    <td class="align-middle"><div class="dropdown">
                                        <a class="btn btn-default btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                            Dropdown link
                                        </a>

                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                                        </ul>
                                        </div>
                                    </td>
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
@endsection
