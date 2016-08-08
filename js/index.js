$("document").ready(function() {
  $("input").focus(function(){
    window.location="search.html";
  });




/*排行*/
var ranking = function(){
    var url = 'php/server.php?inAjax=1&cls=rank&dase=location';
    var data = {};               
    $.get(url, data, function(res) {
        var jsonObj = jQuery.parseJSON(res);
        loadRank(jsonObj);                      
    });     
}
var loadRank =function(res){
    $.each(res,function(i,val){
        $('#rank').append('<a href="#" class="list-group-item">'+res[i].location+'<span class=like_nums>'+res[i].like_nums+'</span></a>');
    });
}
         
ranking();
/*排行结束*/

// 返回顶端
    var offset = 30,
        offset_opacity = 1200,
        scroll_top_duration = 700,
        $back_to_top = $('.cd-top');

    $(window).scroll(function(){
        ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
        if( $(this).scrollTop() > offset_opacity ) { 
            $back_to_top.addClass('cd-fade-out');
        }
    });

    $back_to_top.on('click', function(event){
        event.preventDefault();
        $('body,html').animate({
            scrollTop: 0 ,
            }, scroll_top_duration
        );
    });



});