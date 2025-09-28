;(function($) {
	$(function () {
		tab_hover($('.leftRight .nav .tab-bt'), $('.tab-content'));
		/*选项卡（hover切换）*/
		function tab_hover(bt, content) {
			bt.each(function(i, e) {
				var _ = $(e);
				var _c = _.siblings(content);
				var _bt = _.children().not('.no'),
					_box = _c.children('.tab-box');
				if(_.attr('data-auto') == 'yes'){
					var x,y;
					function xh(){
						x=setTimeout(function(){
							y=_.children(".active").index();
							pd();
						},5000);
					}
					function pd () {
						y=( y <= (_bt.length-2) ? y+1 : 0 );
						var i = _bt.eq(y).data('bt');
						if(_box.filter('[data-img=' + i + ']').length > 0) {
							_bt.eq(y).trigger('mouseenter');	
						}else{
							pd();
						}
					}
					xh();
				}
				_bt.hover(function() {
					if(!$(this).hasClass('active')) {
						if(_.attr('data-auto') == 'yes'){							
							clearTimeout(x);
						}
						var j = $(this).data('bt'),
							_img = _box.filter('[data-img=' + j + ']');
						if(_img.length > 0) {							
							_bt.removeClass('active');
							$(this).addClass('active');
							_box.stop(true,false).hide().removeClass('active').filter('[data-img='+j+']').addClass('active').fadeIn().css("display","block");
						}
						if(_.attr('data-auto') == 'yes'){ xh();}
					}
				}, function() {});
			})
		};
	})
})(jQuery);
