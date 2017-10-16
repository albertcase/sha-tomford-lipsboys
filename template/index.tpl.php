<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>TOM FORD </title>
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="full-screen" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="../src/dist/css/style.css"/>
    <script src="http://tomfordwechat.samesamechina.com/api/v1/js/65454635-a701-4ea4-b7f5-5cebae7e6674/wechat?debug=1"></script>
    <script src="../src/dist/js/all_form_luckydraw.min.js"></script>
</head>
<body class="page-home">
<div id="orientLayer" class="mod-orient-layer">
    <div class="mod-orient-layer__content">
        <i class="icon mod-orient-layer__icon-orient"></i>
        <div class="mod-orient-layer__desc">请在解锁模式下使用竖屏浏览</div>
    </div>
</div>
<!--main content-->
<!-- 已关注 -->
<div class="wrapper animate">
    <!-- z-index is low-->
    <div class="container">
        <!-- 填写表单选项-->
        <div class="pin pin-2" id="pin-fillform">
            <div class="show-img">
                <img src="../src/dist/images/show-1.jpg" alt=""/>
            </div>
            <h3 class="title">
                DATE<br>
                ADDRESS
            </h3>
            <form id="form-contact">
                <div class="form-information">
                    <div class="input-box input-box-name">
                        <input type="text" id="input-name" placeholder="姓名"/>
                    </div>
                    <div class="input-box input-box-mobile">
                        <input type="tel" maxlength="11" id="input-mobile" placeholder="电话"/>
                    </div>
                    <div class="input-box input-box-validate-message-code">
                        <input type="text" id="input-validate-message-code" placeholder="输入短信验证码"/>
                        <div class="btn-get-msg-code">
                            <div class="tt">获取验证码</div>
                        </div>
                    </div>
                    <div class="input-box input-box-timeslot select-box">
                        <input type="text" id="input-text-timeslot" placeholder="预约时间段"/>
                        <select name="timeslot" id="select-timeslot">
                            <option value="">选择时段</option>
                        </select>
                    </div>
                </div>
                <div class="btn btn-submit">
                    <div class="tt">提 交</div>
                </div>
            </form>
        </div>
        <!-- 提交成功 -->
        <div class="pin pin-3" id="pin-result">
            <h3 class="title"></h3>
            <div class="show">
                <img src="../src/dist/images/show-2.jpg" alt=""/>
            </div>
            <div class="submitted-info">
                <div class="t1">
                    提交成功
                </div>
                <div class="details">
                    11月4日 18:00 - 21:00<br>
                    上海市黄浦区半淞园路498号
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>