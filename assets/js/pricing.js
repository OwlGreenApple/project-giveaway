function change_price_list()
{ 
    $(".pricing_list").click(function(){
        $(".pricing_list").removeClass('active');
        var id = $(this).attr('id'); 
        $("#"+id).addClass('active');

        if(id == "month_data")
        {
            $(".month_data").removeClass('d-none');
            $(".tmonth_data").addClass('d-none');
            $(".year_data").addClass('d-none');
        }
        else if(id == "tmonth_data")
        {
            $(".tmonth_data").removeClass('d-none');
            $(".month_data").addClass('d-none');
            $(".year_data").addClass('d-none');
        }
        else
        {
            $(".year_data").removeClass('d-none');
            $(".month_data").addClass('d-none');
            $(".tmonth_data").addClass('d-none');
        }
    }); 
}

function service()
{
    $("select[name='service']").change(function(){
        var val = $(this).val();
        change_service(val);
    });
}

function change_service(val)
{
    var wablas = $(".wablas-server");
    if(val == 1)
    {
        wablas.removeClass('d-none');
        $(".img_wablas").removeClass('d-none'); //on page integration
        $(".img_fonnte").addClass('d-none'); 
    }
    else
    {
        wablas.addClass('d-none');
        $(".img_wablas").addClass('d-none');
        $(".img_fonnte").removeClass('d-none');//on page integration
    } 
}