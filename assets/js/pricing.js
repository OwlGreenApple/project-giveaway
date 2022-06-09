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