$("document").ready(function(){
	
	if(localStorage.location){
		var location = JSON.parse(localStorage.location);
		var time = JSON.parse(localStorage.time);
		$.each(location,function(name,value){
			var html = '';
			$.each(value,function(i,img){
				html+='<a href="#" class="attr_item"><div class="col-xs-4"><img src="'+img+'" width="100%" height="80"/></div><div class="col-xs-2"><p>'+i+'</p></div></a>';
			});
			
			$('.collection').append('<div class="collectList" data-pro="'+name+'"><div class="pro_col"><h2>'+name+'</h2><div class="time_col"><p>收藏于'+time[name]+'</p></div></div></div><div class="attr_col"><div class="row">'+html+'</div></div><p class="seperator">———————————————————————————</p>');
		});
		//$.each(location)
	}
});