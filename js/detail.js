$("document").ready(function(){

	// 异步加载具体内容并填充   此处未从主页获取id值
	      var searchId = window.location.search;//获取search字段
	      var url = "php/details.php" + searchId;
	      var data = {}; 
	      var id=searchId.split('=')[1]; //获取id值

	      $.get(url, data, function(res) {
	          var jsonObj = jQuery.parseJSON(res);
	          // 插入目的地图片
	          $('#img_detail').append('<img src="'+jsonObj.imgs+'"/>');
	          // 插入目的地名称
	          $("#name").text(jsonObj.location);
	          // 插入目的地简介
	          $("#introDetail").html(jsonObj.introtxt);
	          // 插入路线推荐
	          $(".routeDetail").html(jsonObj.routestxt);
	          // 插入景点推荐
	          for(var i=1;i<=jsonObj.attrlen;i++){
	              var d="text_attr"+i;
	              $("#section-1").append(
	                '<div class="attrDetail row"><div class="col-xs-5 img_attr"><img src="'+jsonObj.imgtxt[i]+'"  style="width:100%"/></div><div class="col-xs-7" id='+d+'>'+jsonObj.attrtxt[i]+'</div><p class="seperator">—————————————</p></div>'
	                ); 
	          }

	               // 插入美食推荐
	          $("#section-2").append('<div class="foodDetail row"></div>');
	          for(var j=1;j<=jsonObj.foodlen;j++){
	                    $(".foodDetail").append(
	                      '<div class="col-xs-4"><img src="'+jsonObj.foodsimg[j]+'"  style="width:100%"/><p>'+jsonObj.foodsname[j]+'<p></div></div>'
	                      ); 
	          }

	          $('#like').text(jsonObj.like_nums);
	          isCollect(id);
	       });

//点赞执行函数	
	var thumb = function(id){

		var url = 'php/details.php';
	    var data = {dase:"location",id:id,num:(parseInt($('#like').text())+1)};

		$.post(url, data, function(res) {
	        if(res)                   
	        	$('#like').text(parseInt($('#like').text())+1);
	    });
	}
	
//点赞绑定按钮	
	$('.button').bind('click',function(e){
		e.preventDefault();

		thumb(id);
	});

//获取时间
	 var getCurrentTime = function(){
	   var mydate = new Date();
	   var str = "" + mydate.getFullYear() + "-";
	   str += (mydate.getMonth()+1) + "-";
	   str += mydate.getDate();
	   return str;
	  }
//收藏执行函数
//收藏初始化
var isCollect = function(id){
	var city = $('#name').text();
	if(localStorage.location){
		var temp_location = JSON.parse(localStorage.location); 

		if(temp_location[city]){
			$('#collector').addClass('glyphicon-heart').removeClass('glyphicon-heart-empty');
			$('#collect').text("取消收藏"); 
		}else{
			$('#collector').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');		
			$('#collect').text("收藏");
		}
	}else{
		$('#collector').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');		
		$('#collect').text("收藏");
	}
}
	var collect = function(id){
		var content = $('#collect').text(); 
		if(content == "收藏"){

			$('#collector').addClass('glyphicon-heart').removeClass('glyphicon-heart-empty');

			var a={};
			$.each($('.attrDetail'),function(i,val){
				var temp = $(val);
				a[$(val).find('h2').text()] = $(val).find('img').attr("src");
			});
			var location ={}, time={};
			location[$('#name').text()]=a;
			time[$('#name').text()] = getCurrentTime();

			if(localStorage.location){
				var temp_location = JSON.parse(localStorage.location); 
				$.extend(temp_location,location);
				localStorage.location = JSON.stringify(temp_location);
			}else{
				localStorage.setItem('location',JSON.stringify(location)); 
			}
			if(localStorage.time){
				var temp_time = JSON.parse(localStorage.time); 
				$.extend(temp_time,time);
				localStorage.time = JSON.stringify(temp_time);
			}else{
				localStorage.setItem('time',JSON.stringify(time)); 
			}
			$('#collect').text("取消收藏");

		}else if(content == "取消收藏"){

			$('#collector').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');

			var temp_location1 = JSON.parse(localStorage.location);
			delete temp_location1[$('#name').text()];
			var temp_time1 = JSON.parse(localStorage.time);
			delete temp_time1[$('#name').text()];
			localStorage.location = JSON.stringify(temp_location1);
			localStorage.time = JSON.stringify(temp_time1);  
			$('#collect').text("收藏");
		}
	}
//收藏绑定标签
	$('#collector').bind('click',function(e){
		e.preventDefault();
		collect(id);//id
	});
});