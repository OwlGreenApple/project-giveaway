<div class="row">
    <h3 class="mb-4 account-title"><b><i class="far fa-user main-color"></i>&nbsp;Profile</b></h3>

    <form>
        <div class="form-group mb-4">
            <label>Name:<span class="text-danger">*</span></label>
            <input name="profile_name" value="{{ $user->name }}" type="text" class="form-control form-control-lg"/>
        </div> 
        <div class="form-group mb-4">
            <label>Email:<span class="text-danger">*</span></label>
            <input name="profile_email" value="{{ $user->email }}" type="email" class="form-control form-control-lg"/>
        </div> 
        <div class="row mb-4">
            <div class="form-group col-md-6 col-lg-6">
                <label>Currency:<span class="text-danger">*</span></label>
                <select name="profile_currency" class="form-select form-select-lg">
                    <option value="usd">USD</option>
                    <option value="idr">IDR</option>
                </select>
            </div> 
            <div class="form-group col-md-6 col-lg-6">
                <label>Giveaway Language:<span class="text-danger">*</span></label>
                <select name="profile_lang" class="form-select form-select-lg">
                    <option value="en">English</option>
                    <option value="id">Bahasa</option>
                </select>
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