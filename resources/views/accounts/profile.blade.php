<div class="row">
    <h3 class="mb-4 account-title"><b><i class="far fa-user main-color"></i>&nbsp;Profile</b></h3>

    <form>
        <div class="form-group mb-4">
            <label>Name:<span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-lg" name="user" />
        </div> 
        <div class="form-group mb-4">
            <label>Email:<span class="text-danger">*</span></label>
            <input type="email" class="form-control form-control-lg" name="email" />
        </div> 
        <div class="row mb-4">
            <div class="form-group col-md-6 col-lg-6">
                <label>Currency:<span class="text-danger">*</span></label>
                <select class="form-select form-select-lg">
                    <option>USD</option>
                    <option>IDR</option>
                </select>
            </div> 
            <div class="form-group col-md-6 col-lg-6">
                <label>Giveaway Language:<span class="text-danger">*</span></label>
                <select class="form-select form-select-lg">
                    <option>English</option>
                    <option>Bahasa</option>
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