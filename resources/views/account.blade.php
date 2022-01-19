@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mt-3 px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Account</h1>
        </div>
        <!-- LEFT TAB -->
        <div class="col-md-3 mb-4 px-3">
            <div class="card">
                <div class="px-4 py-4">
                    <a class="settings text-black-50 mn_1 active" data_target="1"><i class="far fa-user"></i>&nbsp;Profile</a>
                    <a class="settings text-black-50 mn_2" data_target="2"><i class="fas fa-exchange-alt"></i>&nbsp;Subscription</a>
                    <a class="settings text-black-50 mn_3" data_target="3"><i class="fab fa-sketch"></i>&nbsp;Branding</a>
                    <a class="settings text-black-50 mn_4" data_target="4"><i class="fas fa-plug"></i>&nbsp;Integrate API</a>
                </div>
            </div>
        </div>

        <!-- RIGHT TAB -->
        <div class="col-md-9 px-3">
            <!-- PROFILE -->
            <div id="settings_target_1" class="card target_hide">
                <div class="card-body px-5 py-5">
                    @include('accounts.profile')
                </div>
            </div>

            <!-- SUBSCRIPTION -->
            <div id="settings_target_2" class="card target_hide d-none">
                <div class="card-body px-5 py-5">
                    @include('accounts.subscription')
                </div>
            </div>

            <!-- BRANDING -->
            <div id="settings_target_3" class="card target_hide d-none">
                <div class="card-body px-5 py-5">
                    @include('accounts.branding')
                </div>
            </div>

            <!-- INTEGRATION -->
            <div id="settings_target_4" class="card target_hide d-none">
                <div class="card-body px-5 py-5">
                    @include('accounts.integration')
                </div>
            </div>

            <!-- end col -->

    <!--  -->
    </div>
</div>

<script>

$(function(){
    data_tabs();
    set_lang_cur();
});

    function data_tabs()
    {
        $(".settings").click(function(){
            var target = $(this).attr('data_target');
            $(".settings").removeClass('active');
            $(".target_hide").addClass('d-none');
            $("#settings_target_"+target).removeClass('d-none');
           
            $(".mn").removeClass('active');
            $(".mn_"+target).addClass('active');
        });
    }

    //PROFILE
    function set_lang_cur()
    {
        var currency = '{{ $user->currency }}';
        var lang = '{{ $user->lang }}';

        console.log(lang);
        $("select[name='profile_currency'] option[value='"+currency+"']").prop('selected',true);
        $("select[name='profile_lang'] option[value='"+lang+"']").prop('selected',true);
    }

</script>
@endsection
