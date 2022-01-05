@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white">
                    <h2 class="float-start align-middle ">Your Giveaways</h2>
                    <div class="float-end align-middle "><button class="btn btn-default bg-custom text-white">New Giveaway</button></div>
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
                            <tr>
                                <td class="align-middle"><span class="main-color">aaa</span><br/>Jan 5 2022</td>
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
