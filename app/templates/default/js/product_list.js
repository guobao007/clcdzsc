$(function () {
    var hasmore = $("input[name=hasmore]").val();
    var page_total = $("input[name=page_total]").val();
    var curpage = $("input[name=curpage]").val();//分页
    var page_html = '';
    if(curpage <= 0){
        curpage = 1;
    }else if(curpage > page_total){
        curpage = page_total;
    }
    if (!hasmore) {
        $('.next-page').addClass('disabled');
    }
    for (var i = 1; i <= page_total; i++) {
        if (i == curpage) {
            page_html += '<option value="' + i + '" selected>' + i + '</option>';
        } else {
            page_html += '<option value="' + i + '">' + i + '</option>';
        }
    }

    $('select[name=page_list]').empty();
    $('select[name=page_list]').append(page_html);
    
    
    $("#product_list").empty();

    if (curpage > 1) {
        $('.pre-page').removeClass('disabled');
    } else {
        $('.pre-page').addClass('disabled');
    }

    if (curpage < page_total) {
        $('.next-page').removeClass('disabled');
    } else {
        $('.next-page').addClass('disabled');
    }
    

    $("select[name=page_list]").change(function () {
        var curpage = $('select[name=page_list]').val();
        var url = ApiUrl + pUrl + curpage;
        window.location.href = url;
    });
    
    $('.pre-page').click(function(){//上一页
        if(page_total==1 || curpage <=1){
            return false;
        }
        curpage = Number(curpage) - 1;
        var url = ApiUrl + pUrl + curpage;
        window.location.href = url;
    });
    
    $('.next-page').click(function(){//下一页
        if(!hasmore){
            return false;
        }
        curpage = Number(curpage) + 1;
        var url = ApiUrl + pUrl + curpage;
        window.location.href = url;
    });
});