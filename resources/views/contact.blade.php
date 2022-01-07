@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Contact Us</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8">
            <form>
                <div class="form-group mb-3">
                    <label>Name:<span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" name="user" />
                </div> 

                <div class="form-group mb-3">
                    <label>Your Message:<span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-lg" name="message"></textarea>
                </div> 

                <button type="submit" class="btn bg-custom btn-lg text-white">Send</button>
            </form>
        <!-- end col -->
        </div>
    </div>
</div>

@endsection