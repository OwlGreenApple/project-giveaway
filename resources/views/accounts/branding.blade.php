<div class="row">
    <h3 class="mb-4 account-title"><b><i class="fab fa-sketch main-color"></i>&nbsp;{{ Lang::get('title.branding') }}</b></h3>
    <span id="brd"><!-- --></span>
    <div class="text-justify fst-italic border-bottom py-3 mb-4">{{ Lang::get('table.branding') }}</div>
    <form id="upload_branding">
        <input type="file" name="logo_branding" class="form-control form-control-lg mb-2" />
        <span class="text-danger err_logo_branding"><!--  --></span>
        <input type="text" placeholder="ex : {{ Lang::get('table.ex') }}" value="{{ $user->brand_link }}" name="link_branding" class="form-control form-control-lg mb-2" />
        <span class="text-danger err_link_branding"><!--  --></span>
        <div><button type="submit" class="btn bg-custom btn-lg text-white mt-2 btn-account">{{ Lang::get('table.update') }}</button></div>
    </form>
</div>