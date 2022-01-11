@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-md-9 px-0 wrapper">
            <!-- youtube or banner carousel -->
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://media.istockphoto.com/photos/colorful-holiday-carousel-horse-xxxlarge-picture-id171345759" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="https://images.unsplash.com/photo-1561424412-6c2125ecb1cc?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="https://images.unsplash.com/photo-1502136969935-8d8eef54d77b?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=869&q=80" class="d-block w-100" alt="...">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            <!-- end carousel -->

            <div class="col-lg-9 mx-auto">
                <div class="contest-title mt-3">TESTING</div>
                <div class="time-contest">Time Left</div>
                <div class="container">
                    <div id="countdown" class="text-center mt-3">
                        <ul>
                            <li><div class="count" id="days"></div><div class="count-label">Days</div></li>
                            <li class="count-space"><span class="count">:</span></li>
                            <li><div class="count" id="hours"></div><div class="count-label">Hours</div></li>
                            <li class="count-space"><span class="count">:</span></li>
                            <li><div class="count" id="minutes"></div><div class="count-label">Minutes</div></li>
                            <li class="count-space"><span class="count">:</span></li>
                            <li><div class="count" id="seconds"></div><div class="count-label">Seconds</div></li>
                        </ul>
                    </div>
                    <!-- end timer -->
                </div>
                <h4 class="text-center mb-3"><span class="me-2"><i class="fas fa-gift main-color"></i> Prize : Rp.300.000,00</span><span class="ms-2"><i class="fas fa-trophy trophy"></i> Total Winner : 3</span></h4>
                <div class="text-center mt-4"><h2>Enter your WhatsApp <i class="fab fa-whatsapp"></i> Number</h2></div>
                <div class="iti-wrapper">
                    <div class="input-group">
                        <input type="text" id="phone" name="phone" class="form-control form-control-lg" required/>
                        <span class="error phone"></span>
                        <input id="hidden_country_code" type="hidden" class="form-control" name="code_country" />
                        <input name="data_country" type="hidden" /> 
                        <span class="error code_country"></span>

                        <button type="submit" class="btn bg-custom btn-lg text-white">Enter</button>
                    </div>
                </div>
                <div class="terms mt-4">Aturan main</div>
                <div class="row px-3 py-3">
                    <div class="col-lg-6 text-end">Giveaway timezone : +7</div>
                    <div class="col-lg-6 text-start">Offered By : <a class="main-color">aaaa</a></div>
                </div>
                <!-- end container -->
            </div>
        <!-- end col -->
    </div>
</div>

<script>
    var global_date = "Jan 10, 2022 16:45:00"; //set target event date
</script>
<script src="{{ asset('assets/js/countdowntimer.js') }}"></script>
@endsection
