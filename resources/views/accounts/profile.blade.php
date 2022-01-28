<div class="row">
    <h3 class="mb-4 account-title"><b><i class="far fa-user main-color"></i>&nbsp;Profile</b></h3>

    <form id="profile">
        <div class="form-group mb-4">
            <label>Name:<span class="text-danger">*</span></label>
            <input name="profile_name" value="{{ $user->name }}" type="text" class="form-control form-control-lg"/>
            <span class="text-danger err_profile_name"><!-- --></span>
        </div> 
        <div class="form-group mb-4">
            <label>Password: <span class="text-info">{{ Lang::get('custom.profile_password') }}</span></label>
            <input name="password" type="password" class="form-control form-control-lg"/>
            <span class="text-danger err_password"><!-- --></span>
        </div> 
        <div class="form-group mb-4">
            <label>ReType Password: <span class="text-info">{{ Lang::get('custom.profile_password') }}</span></label>
            <input name="password_confirmation" type="password" class="form-control form-control-lg"/>
            <span class="text-danger err_password_confirmation"><!-- --></span>
        </div> 
        <div class="row mb-4">
            <div class="form-group col-md-6 col-lg-6">
                <label>Currency:<span class="text-danger">*</span></label>
                <select name="profile_currency" class="form-select form-select-lg">
                @if(count($helper::currency()) > 0)
                    @foreach($helper::currency() as $key=>$val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                @endif
                </select>
                <span class="text-danger err_profile_currency"><!-- --></span>
            </div> 
            <div class="form-group col-md-6 col-lg-6">
                <label>Giveaway Language:<span class="text-danger">*</span></label>
                <select name="profile_lang" class="form-select form-select-lg">
                @if(count($helper::lang()) > 0)
                    @foreach($helper::lang() as $key=>$val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                @endif
                </select>
                <span class="text-danger err_profile_lang"><!-- --></span>
            </div>
        </div>

        <button type="submit" class="btn bg-custom btn-lg text-white mt-2 btn-account">Update</button>
    <!-- end form -->
    </form>
</div>

<div class="col-lg-12 mt-5">
    <h3 class="mb-4 account-title"><b>API Information</b></h3>
    <div class="form-group mb-4">
        <label>API secret:</label>
        <input type="text" class="form-control form-control-lg" />
    </div> 
</div>