var Scoll = {
	num: parseInt($('.scoll img').length), 
	width: parseInt($('.scoll img:eq(0)').width()), 
	isRunning: false,
	ready_moved :true,  //判断每次滑动开始的标记变量
	touchX:0,
	minRange:30,

	turn: function(offset){
		var that = Scoll,
			slide_num = that.num-2,
			oScoll = $('.scoll'),
			width = parseInt(that.width);

		
		that.isRunning = true;


        var left = parseInt(oScoll.css('left')) + offset;

        oScoll.animate({'left': left}, 300, function () {
            if(left > -200){
                oScoll.css('left', -width * slide_num);
            }
            if(left < (-width * slide_num)) {
                oScoll.css('left', -width);
            }
            that.isRunning = false;
        });

		//this.turnId = num;
		$('.pages div').removeClass();
		$('.pages div[pageNum = "'+ that.turnId+'"]').addClass('focus');
		
	},
	turnTo: function(num){
		var that = Scoll;
		that.destroy();
		this.turn(num);
		that.open();
	},
	makePages: function(){
		var html = '<div class="pages">',
			num = this.num - 2;
		for (var i = 1; i <= num; i++){
			html += '<div pageNum="'+i+'"></div>'
		}
		html += '</div>';
		$('.scollCont').append(html);
		$('.pages').css('width', 32*num+'px');
		$('.pages').css('left',(this.width-$('.pages').width())/2+'px');
		$('.pages div[pageNum="1"]').addClass('focus');
	},
	open: function(){
		var that = this;
		if (!that.setId){
			
		that.setId = setTimeout(function () {
	            $('.btnRight').trigger('click');
	            Scoll.open();
            }, 2000);
		}		
	},
	destroy: function(){
		var that = this;
		if (!!that.setId){
			clearInterval (that.setId);
			that.setId = null;
		}
	},
	bind: function(){
		var that = Scoll;
		var slide_num = that.num -2;
		$('.btnLeft').css('visibility', 'hidden');
		$('.btnRight').css('visibility', 'hidden');
		$('.btnLeft').on('click', function(){
			if (that.isRunning){
			    return false;
		    }
		    if(that.turnId == 1){
            	that.turnId = slide_num;
            }else {
            	that.turnId --;
            }
			that.turnTo(that.width);
		});
		$('.btnRight').on('click', function(){
			if (that.isRunning){
			    return false;
		    }
		     if(that.turnId == slide_num){
            	that.turnId = 1;
            }else{
            	that.turnId ++;
            }
			that.turnTo(-that.width);
		});
		$('.pages div').on('click',function(){
			var myIndex = parseInt($(this).attr('pageNum'));
            var offset = -that.width * (myIndex - that.turnId);
            that.turn(offset);
            that.turnId = myIndex;
			$('.pages div').removeClass();
			$('.pages div[pageNum = "'+ myIndex+'"]').addClass('focus');
		})
		$('.scollCont').on('mouseenter',function(){
			$('.btnLeft').css('visibility', 'visible');
			$('.btnRight').css('visibility', 'visible');
			that.destroy();
		}).on('mouseleave',function(){
			that.open();
			$('.btnLeft').css('visibility', 'hidden');
			$('.btnRight').css('visibility', 'hidden');
		});
		$('.scollCont').on('touchstart',function(e){
			console.log(e.originalEvent);
			that.start(e.originalEvent);
		}).on('touchmove',function(e){
			that.move(e.originalEvent);
		}).on('touchend',function(e){
			that.move(e.originalEvent);
		});
	},
	start: function(e) {
		if (Scoll.ready_moved) {

		   var touch = e.touches[0];		  
		   Scoll.touchX = touch.pageX;
		   Scoll.ready_moved = false;
		}
	},
	move: function(e) {
		e.preventDefault();
		var minRange = Scoll.minRange,
		touchX = Scoll.touchX;
		 //alert(touchX);

		if (!Scoll.ready_moved) {

		    var release = e.touches[0],
		    releasedAt = release.pageX;

		    if (releasedAt + minRange < touchX) {
		      Scoll.ready_moved = true;
		      $('.btnRight').trigger('click');
		    } 
		    else if (releasedAt - minRange > touchX) {
		      $('.btnLeft').trigger('click');
		      Scoll.ready_moved = true;
		    }
		}
    },
	init: function(){
		this.turnId = 1;
		//this.reset();
		this.makePages();
		this.open();
		this.bind();
	}
}

Scoll.init();