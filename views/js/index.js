 var show_per_page = 4; 
    var current_page = 0;

    function set_display(first, last) {
        $('#content > div.panel-primary').css('display', 'none');
        $('#content > div.panel-primary').slice(first, last).css('display', 'block');
    }

    function previous(){
        if($('.active').prev('.page_link').length) go_to_page(current_page - 1);
    }

    function next(){
        if($('.active').next('.page_link').length) go_to_page(current_page + 1);
    }

    function go_to_page(page_num){
        current_page = page_num;
        start_from = current_page * show_per_page;
        end_on = start_from + show_per_page;
        set_display(start_from, end_on);
        $('.active').removeClass('active');
        $('#id' + page_num).addClass('active');
    }  

    $(document).ready(function() {

        var number_of_pages = Math.ceil($('#content > div.panel-primary').length / show_per_page);

        var nav = '<ul class="pagination"><li><a href="javascript:previous();">&laquo;</a>';

        var i = -1;

        while(number_of_pages > ++i){
            nav += '<li class="page_link'
            if(!i) nav += ' active';
            nav += '" id="id' + i +'">';
            nav += '<a href="javascript:go_to_page(' + i +')">'+ (i + 1) +'</a>';
        }

        nav += '<li><a href="javascript:next();">&raquo;</a></ul>';

        $('#page_navigation').html(nav);
        set_display(0, show_per_page);
    });