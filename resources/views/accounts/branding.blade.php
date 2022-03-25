<div class="row">
    <h3 class="mb-4 account-title"><b><i class="fab fa-sketch main-color"></i>&nbsp;Branding</b></h3>
    <span id="brd"><!-- --></span>
    <div class="text-justify fst-italic border-bottom py-3 mb-4">As a premium member you can upload your own logo to be used in the footer of giveaways and related emails.</div>
    <form id="upload_branding">
        <input type="file" name="logo_branding" class="form-control form-control-lg mb-2" />
        <span class="text-danger err_logo_branding"><!--  --></span>
        <input type="text" placeholder="ex : https://yourlink.com" value="{{ $user->brand_link }}" name="link_branding" class="form-control form-control-lg mb-2" />
        <span class="text-danger err_link_branding"><!--  --></span>
        <div><button type="submit" class="btn bg-custom btn-lg text-white mt-2 btn-account">Update</button></div>
    </form>
</div>