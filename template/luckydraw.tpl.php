<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>KENZO </title>
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="full-screen" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="../src/dist/css/style.css"/>
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?b22f81798afe9dabe732071d61f2e976";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <script src="http://kenzowechat.samesamechina.com/weixin/jssdkforsite"></script>
    <!-- userflow-->
    <script type="text/javascript">
        var userInfo = {
            isSubmit: <?php echo $conf['isSubmit']; ?>, /*是否提交了用户详细信息表单*/
            isLuckyDraw: <?php echo $conf['isLuckyDraw']; ?>, /*是否抽奖*/
            remaintimes: <?php echo $conf['remaintimes']; ?> /*剩余抽奖次数*/
        };
    </script>
    <script src="../src/dist/js/all_form_luckydraw.min.js"></script>
</head>
<body class="page-home">
<div id="orientLayer" class="mod-orient-layer">
    <div class="mod-orient-layer__content">
        <i class="icon mod-orient-layer__icon-orient"></i>
        <div class="mod-orient-layer__desc">请在解锁模式下使用竖屏浏览</div>
    </div>
</div>
<div class="preload">
    <div class="animate-flower">
        <!--<img src="../src/dist/images/preload-flower.jpg" alt="kenzo"/>-->
    </div>
    <div class="loading-num">
        ...<span class="num">10</span>%
    </div>
</div>
<!--main content-->
<!-- 已关注 -->
<div class="wrapper animate">
    <!-- sometimes z-index is larger than border-frame, sometimes is lower-->

    <!-- z-index is middle-->
    <div class="border-frame">
        <div class="bf bf-1"></div>
        <div class="bf bf-2"></div>
        <div class="bf bf-3"></div>
    </div>
    <div class="foreground">
        <div class="fb-flower fb-1">
            <img src="../src/dist/images/flower-bottom-2.png" alt="kenzo"/>
        </div>
        <div class="fb-flower fb-2">
            <img src="../src/dist/images/flower-bottom-1.png" alt="kenzo"/>
        </div>
    </div>
    <div class="logo">
        <img src="../src/dist/images/logo.png" alt="kenzo"/>
    </div>
    <!-- z-index is low-->
    <div class="container">
        <!-- 提示抽奖页面 -->
        <div class="pin pin-1" id="pin-landing">
            <div class="t1">
                <img src="../src/dist/images/tips-1.png" alt="kenzo"/>
            </div>
            <div class="lucky-info">
                您共计签到 <span class="totaldays">X</span> 天，共有 <span class="totaltimes">X</span> 次抽奖机会<br>
                快点击“抽奖”按钮<br>
                即有机会获得睡美人面膜（75ML）哦！
                <!--很遗憾，您没有中奖！-->
                <!--再次点击“抽奖”试试看吧！-->
            </div>
            <div class="product-show">
                <img src="../src/dist/images/main-flower.png" alt="kenzo"/>
            </div>
            <div class="btn btn-start-luckydraw">
               <div class="tt"> 抽  奖 <span class="times">(剩<span class="remaintimes">0</span>次)</span></div>
            </div>
            <div class="des">
                花颜悦人 美由情生<br>
                源自植物的舒柔呵护<br>
                令肌肤绽放青春光彩
            </div>
            <div class="link-terms">*规则与条款</div>
        </div>
        <!-- 填写表单选项-->
        <div class="pin pin-2" id="pin-fillform">
            <h3 class="title">
                *请确认您的邮寄信息填写无误<br>
                以便我们为您更快寄出产品
            </h3>
            <form id="form-contact">
                <div class="form-information">
                    <div class="input-box input-box-name">
                        <input type="text" id="input-name" placeholder="姓名"/>
                    </div>
                    <div class="input-box input-box-mobile">
                        <input type="tel" maxlength="11" id="input-mobile" placeholder="电话"/>
                    </div>
                    <div class="input-box input-box-validate-code">
                        <input type="text" id="input-validate-code" placeholder="输入验证码"/>
                        <div class="validate-code">
                            <span class="validate-code-img"></span>
                            <span class="code-text">看不清楚？换张图片</span>
                        </div>
                    </div>
                    <div class="input-box input-box-validate-message-code">
                        <input type="text" id="input-validate-message-code" placeholder="输入短信验证码"/>
                        <div class="btn btn-get-msg-code">
                            <div class="tt">获取验证码<span class="second">(60s)</span></div>
                        </div>
                    </div>
                    <div class="input-box input-box-province select-box">
                        <input type="text" id="input-text-province" placeholder="省份"/>
                        <select name="province" id="select-province">
                            <option value="">省份</option>
                        </select>
                    </div>
                    <div class="input-box input-box-city-district">
                        <div class="select-box">
                            <input type="text" id="input-text-city" placeholder="城市"/>
                            <select name="city" id="select-city">
                                <option value="">城市</option>
                            </select>
                        </div>
                        <div class="select-box">
                            <input type="text" id="input-text-district" placeholder="区县"/>
                            <select name="district" id="select-district">
                                <option value="">区县</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-box input-box-address">
                        <input type="text" id="input-address" placeholder="详细地址"/>
                    </div>
                </div>
                <div class="btn btn-submit">
                    <div class="tt">提 交</div>
                </div>
            </form>
            <!--<div class="foreground">-->
                <!--<img src="../src/dist/images/foreground-1.png" alt="kenzo"/>-->
            <!--</div>-->
        </div>
        <!-- 抽奖结果显示 -->
        <div class="pin pin-3" id="pin-result">
            <div class="v-content">
                <h3 class="title">「提交成功」</h3>
                <div class="des">
                    Miss K将会在30个工作日内<br>
                    将花颜舒柔夜间修护面膜（75ml）寄出<br>
                    请您耐心等候哦~<br>
                    云朵般的面膜质地，悠享睡美人的梦幻夜
                </div>
            </div>
        </div>
        <div class="pin pin-4">
            <div class="v-content">
                <h3 class="title">「活动提示」</h3>
                <div class="des">
                    很遗憾，您之前未参加签到活动<br>
                    暂无抽奖机会<br>
                    敬请持续关注KENZO官方微信<br>
                    更多福利活动等你哦！
                </div>
            </div>
        </div>

    </div>
</div>

<!-- z-index is high-->
<div class="popup terms-pop">
    <div class="inner">
        <h3 class="title">·活动条款·</h3>
        <div class="pcontent">
            <h4 class="subtitle">活动时间</h4>
            <p class="des activity-time">
                2017年11月11日至2017年11月30日
            </p>
            <h4 class="subtitle">参与条件</h4>
            <p class="des activity-requirement">
                参与此次KENZO签到活动的用户<br>
                即可参与抽奖活动<br>
                累计签到次数即为抽奖次数
            </p>
            <h4 class="subtitle">奖品内容</h4>
            <p class="des activity-prize">
                奖品为KENZO花颜舒柔夜间修护面膜正装（75ml）<br>
                根据中奖用户填写的邮寄地址<br>
                在中奖后的30个工作日内寄送<br>
                奖品限量66个
            </p>
            <p class="tips-2">* 每个微信ID仅限中奖1次<br>中奖名单将会在活动结束后于KENZO官方微信公布</p>
            <!--<p class="product-name">* KENZO花颜舒柔夜间修护面膜</p>-->
        </div>
        <div class="btn-close">X</div>
    </div>
</div>
<!--<div class="popup pop-lottery-result show" id="popup-result-yes">-->
    <!--<div class="inner">-->
        <!--<div class="f-2"></div>-->
        <!--<div class="msg">-->
            <!--<div class="f-1"></div>-->
            <!--<div class="f-3"></div>-->
            <!--<div class="result-content">-->
                <!--<h3 class="subtitle">-->
                    <!--<span>很遗憾，您没有中奖</span>-->
                <!--</h3>-->
                <!--<div class="des">请持续关注KENZO官方微信，<br>-->
                    <!--更多福利等着你哦！</div>-->
            <!--</div>-->
        <!--</div>-->
        <!--<div class="btn-close">关闭</div>-->
    <!--</div>-->
<!--</div>-->
</body>
</html>