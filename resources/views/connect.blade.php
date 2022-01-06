@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Connect WA</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8">
            <form>
                <div class="card px-5 py-5">
                    <div class="card-body p-0">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="628xxxxxx">
                            <button class="btn bg-custom text-white" type="button">Connect</button>
                        </div>

                        <table class="table table-striped mt-4 mb-0">
                            <thead>
                                <tr>
                                    <th>WA Number</th>
                                    <th>Status</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>62811111</td>
                                    <td><span class="text-success">Connected</span></td>
                                    <td><span class="text-danger"><i class="far fa-trash-alt"></i></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        <!-- end col -->
        </div>
    </div>
</div>

@endsection