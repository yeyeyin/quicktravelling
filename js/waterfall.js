$("document").ready(function(){
    var page = 0;
    var isLoading = true;
    var flag=false;

    var waterfallAdd = function(parent,imgData){
        $.each(imgData,function(index,data){
           parent.append('<a href="http://localhost/showtourist/details.html?id='+imgData[index].id+'"class="list-group-item suojin"><div class="row"><div class="col-xs-6"><img class="lazy" data-original="'+imgData[index].imgs+'"width="100%" height="100"/></div><div class="col-xs-6"><h2>'+imgData[index].location +'</h2><p>'+ imgData[index].tips+'</p></div></div><p class="seperator">——————————————————</p></a>'); 
        });
    }

    $("img.lazy").lazyload({effect:"fadeIn"});

    $('#waterfall').on('click','a',function(e){

        window.event? window.event.returnValue = false : e.preventDefault();
        window.open($(this).attr("href"));
    });
    $(window).bind('scroll',(function(){
         
         if ($(document).height() - $(this).scrollTop() - $(this).height()<40){
            if(isLoading){
                isLoading = false;
                                   
                var url = 'php/server.php?inAjax=1&cls=waterfall&page='+ page++ +'&dase=location';
                var data = {};

                if (page == 2){
                    page = 0;
                }                
                $.get(url, data, function(res) {
                    var jsonObj = jQuery.parseJSON(res);                   
                    waterfallAdd($('#waterfall'),jsonObj);
                    $("img.lazy").lazyload({effect:"fadeIn"});
                    isLoading = true;
                });           
            }
         }
    }));
    
});