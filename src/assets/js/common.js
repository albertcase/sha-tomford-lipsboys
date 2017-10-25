;(function(){
	var ua = navigator.userAgent.toLowerCase();
	var Common = {
		gotoPin:function(num){
			$('.wrapper .pin').removeClass('current');
			$('.wrapper .pin').eq(num).addClass('current');
		},
		getParameterByName:function(name){
			name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
			var results = regex.exec(location.search);
			return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		},
		setParameterByName:function(name,value){
			var query = location.search.substring(1,location.search.length);
			result = query;
			var queryArr = query.split('&');
			//update arr
			var newQuery = '';
			for(var i=0;i<queryArr.length;i++){
				if(queryArr[i].indexOf(name)>-1){
					queryArr[i] = name + '=' + value;
				}
				newQuery = newQuery+queryArr[i]+'&';
			};

			return '?'+newQuery;
		},
        hashRoute:function(){
            var hasTag = location.hash;
            if(hasTag.indexOf('#page=')>-1){
                var hashArr = hasTag.split('=');
                //console.log()
                if(hashArr[1] < $('.pin').length){
                    Common.gotoPin(hashArr[1]);
                }else{
                    Common.gotoPin(0);
                }
            }
        },
		msgBox:{
			add:function(msg,long){
				if(long){
					$('body').append('<div class="ajaxpop msgbox minwidthbox"><div class="loading">'+msg+'</div></div>');
				}else{
					$('body').append('<div class="ajaxpop msgbox"><div class="loading"><div class="icon-loading"></div>'+msg+'</div></div>');
				}
			},
			remove:function(){
				$('.ajaxpop').remove();
			}
		},
		errorMsg : {
			add:function(ele,msg){

				for(var i in ele.childNodes){
					if(ele.childNodes[i].className == 'error'){
						ele.childNodes[i].textContent = msg;
						return true;
					}else{
						if(i==ele.childNodes.length-1){
							var newDiv = document.createElement('div');
							newDiv.textContent = msg;
							newDiv.className = 'error';
							ele.appendChild(newDiv);
						}
					}
				}
			},
			remove:function(ele){

				for(var i in ele.childNodes){
					if(ele.childNodes[i].className == 'error'){
						ele.childNodes[i].parentNode.removeChild(ele.childNodes[i]);
						return;
					}
				}
			}
		},
		errorMsgBox : {
			add:function(msg){
				if(!$('.msgbox').length){
					$('#pin-fillform').append('<div class="msgbox">'+msg+'</div>');
				}else{
					$('#pin-fillform .msgbox').html(msg);
				}
				var rvMsgBox = setTimeout(function(){
					$('.msgbox').remove();
				},3000);
			},
			remove:function(ele){
				if($('.msgbox').length){
					$('.msgbox').remove();
				}
			}
		},
		alertBox:{
			add:function(msg){
				$('body').append('<div class="alertpop msgbox"><div class="inner"><div class="msg">'+msg+'</div><div class="btn-alert-ok">关闭</div></div></div>');
			},
			remove:function(){
				$('.alertpop').remove();
			}
		},
        //lotteryResultPop:{
        //    add:function(id,title,des){
        //        var lotteryHtml = '<div class="popup pop-lottery-result show" id="'+id+'">'+
        //            '<div class="inner">'+
        //            '<div class="f-2"></div>'+
        //            '<div class="msg">'+
        //            '<div class="f-1"></div>'+
        //            '<div class="f-3"></div>'+
        //            '<div class="result-content">'+
        //            '<h3 class="subtitle">'+
        //            '<span>'+title+'</span>'+
        //            '</h3>'+
        //            '<div class="des">'+des+'</div>'+
        //            '</div>'+
        //            '</div>'+
        //            '<div class="btn-close">关闭</div>'+
        //            '</div>'+
        //            '</div>';
        //        $('body').append(lotteryHtml);
        //    },
        //    remove:function(){
        //        $('.pop-lottery-result').remove();
        //    }
        //},
		overscroll: function(el){
			el.addEventListener('touchstart', function() {
				var top = el.scrollTop
					, totalScroll = el.scrollHeight
					, currentScroll = top + el.offsetHeight
				//If we're at the top or the bottom of the containers
				//scroll, push up or down one pixel.
				//
				//this prevents the scroll from "passing through" to
				//the body.
				if(top === 0) {
					el.scrollTop = 1
				} else if(currentScroll === totalScroll) {
					el.scrollTop = top - 1
				}
			});
			el.addEventListener('touchmove', function(evt) {
				//if the content is actually scrollable, i.e. the content is long enough
				//that scrolling can occur
				if(el.offsetHeight < el.scrollHeight)
					evt._isScroller = true
			})
		},


	};


	this.Common = Common;

}).call(this);

$(document).ready(function(){

//	close alert pop
	$('body').on('touchstart','.btn-alert-ok',function(){
		$(this).parent().parent('.alertpop').remove();
	});
    //Common.overscroll(document.querySelector('.wrapper'));
    document.body.addEventListener('touchmove', function(evt) {
        if(!evt._isScroller) {
            evt.preventDefault();
        }
    });

});



