$(document).ready(function(){
    wx.ready(function(){
        wx.hideAllNonBaseMenuItem();
    });
    var curCode = Common.getParameterByName('code');
    //generate qrcode
    if($('#generate-qrcode').length){
        new QRCode(document.getElementById('generate-qrcode'), curCode);
    }

//    for consume page
//    已经核销
//    if(isConsumed == '1'){
//        $('.btn-check').addClass('hide');
//        $('.msg').removeClass('hide').html('已核销');
//    }

    $('.btn-check').on('touchstart', function(){
        Common.msgBox.add('loading...');
        $.ajax({
            url:'/api/consume',
            type:'POST',
            dataType:'json',
            data:{
                code:curCode
            },
            success:function(data){
                Common.msgBox.remove();
                if(data.status==1){
                    $('.btn-check').addClass('hide');
                    $('.msg').removeClass('hide');
                }else{
                    Common.alertBox.add(data.msg);
                }

            }
        });
    });


});