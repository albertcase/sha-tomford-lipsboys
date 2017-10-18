//;(function(){
//
//    this.weixinshare = weixinshare;
//}).call(this);
function weixinshare(obj,successCallBack){
    //wx.config({
    //    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
    //    appId: data.appId, // 必填，公众号的唯一标识
    //    timestamp: data.timestamp, // 必填，生成签名的时间戳
    //    nonceStr: data.nonceStr, // 必填，生成签名的随机串
    //    signature: data.signature,// 必填，签名，见附录1
    //    jsApiList: ['onMenuShareAppMessage','onMenuShareTimeline'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    //});
    wx.ready(function(){

        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
        wx.onMenuShareAppMessage({
            title: obj.title1,
            desc: obj.des,
            link: obj.link,
            imgUrl: obj.img,
            type: '',
            dataUrl: '',
            success: function () {
                _hmt.push(['_trackEvent', 'wechat', 'share', 'shareOnMenuShareAppMessage']);
                successCallBack();

            },
            cancel: function () {

            }
        });
        wx.onMenuShareTimeline({
            title: obj.title1,
            link: obj.link,
            imgUrl: obj.img,
            success: function () {
                _hmt.push(['_trackEvent', 'wechat', 'share', 'shareOnMenuShareTimeline']);
                successCallBack();
            },
            cancel: function () {

            }
        });
    });
};

$(document).ready(function(){
    weixinshare({
        title1: 'TOM FORD Lips & Boys唇魅之夜',
        des: '点击注册，邂逅你的唇间男生',
        link: window.location.href,
        img: window.location.origin+'/src/dist/images/share.jpg'
    },function(){
        console.log('sharesuccess');
    });

});