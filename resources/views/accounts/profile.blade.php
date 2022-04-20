<div class="row">
    <h3 class="mb-4 account-title"><b><i class="far fa-user main-color"></i>&nbsp;{{ Lang::get('title.profile') }}</b></h3>

    <form id="profile">
        <div class="form-group mb-4">
            <label>{{ Lang::get('table.name') }}<span class="text-danger">*</span></label>
            <input name="profile_name" value="{{ $user->name }}" type="text" class="form-control form-control-lg"/>
            <span class="text-danger err_profile_name"><!-- --></span>
        </div>
        <div class="form-group mb-4">
            <label>{{ Lang::get('table.password') }} <span class="text-info">{{ Lang::get('custom.profile_password') }}</span></label>
            <input name="password" type="password" class="form-control form-control-lg"/>
            <span class="text-danger err_password"><!-- --></span>
        </div>
        <div class="form-group mb-4">
            <label>{{ Lang::get('table.password.retype') }}: <span class="text-info">{{ Lang::get('custom.profile_password') }}</span></label>
            <input name="password_confirmation" type="password" class="form-control form-control-lg"/>
            <span class="text-danger err_password_confirmation"><!-- --></span>
        </div>
        @if($user->is_admin == 1)
        <div class="form-group mb-4">
            <label>Ganti Persentasi (%): <span class="text-info">Ini buat ganti jumlah persentasi yg didapat oleh affiliate</span></label>
            <input name="percentage" value="{{ $user->percentage }}" type="number" min="0" max="100" class="form-control form-control-lg"/>
        </div>
        @endif
        <!-- 
        {{-- <div class="row mb-4">
            <div class="form-group col-md-6 col-lg-6">
                <label>{{ Lang::get('table.lang') }}:<span class="text-danger">*</span></label>
                <select name="profile_lang" class="form-select form-select-lg">
                @if(count($helper::lang()) > 0)
                    @foreach($helper::lang() as $key=>$val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                @endif
                </select>
                <span class="text-danger err_profile_lang"></span>
            </div>
        </div> --}} -->

        <button type="submit" class="btn bg-custom btn-lg text-white mt-2 btn-account">{{ Lang::get('table.update') }}</button>
    <!-- end form -->
    </form>
</div>

<!-- <div class="col-lg-12 mt-5">
    <h3 class="mb-4 account-title"><b>API Information</b></h3>
    <div class="form-group mb-4">
        <label>API secret:</label>
        <input type="text" class="form-control form-control-lg" />
    </div>
</div> -->
