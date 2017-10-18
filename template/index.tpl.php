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
    <script>
        var isReservation = <?php echo $conf['isReservation']; ?>;
        var maxNumber = <?php echo $conf['maxNumber']; ?>; // 有库存是1，没有库存是0
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
                TOM FORD Lips & Boys唇魅之夜<br>
                将于11月3-4日在上海世博创意秀场奢华呈献<br>
                即刻点击注册，邂逅你的唇间男孩
            </h3>
            <form id="form-contact">
                <div class="form-information">
                    <div class="input-box input-box-name">
                        <input type="text" id="input-name" placeholder="姓名"/>
                    </div>
                    <div class="input-box input-box-mobile">
                        <input type="tel" maxlength="11" id="input-mobile" placeholder="电话号码"/>
                    </div>
                    <div class="input-box input-box-validate-message-code">
                        <input type="text" id="input-validate-message-code" placeholder="验证码"/>
                        <div class="btn-get-msg-code">
                            <div class="tt">验证</div>
                        </div>
                    </div>
                    <div class="input-box input-box-timeslot select-box">
                        <input type="text" id="input-text-timeslot" placeholder="选择时段"/>
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
                    <span class="date"><?php echo $conf['applyDate']; ?></span><br>
                    上海市黄浦区半淞园路498号
                </div>
            </div>
        </div>
        <!-- 店铺列表-->
        <div class="pin pin-4" id="pin-shoplists">
            <div class="show-img">
                <img src="../src/dist/images/show-1.jpg" alt=""/>
            </div>
            <h3 class="title">
                感谢您的支持！<br>
                非常抱歉，现在席位已满。<br>
                欢迎您莅临TOM FORD美妆专柜，<br>
                了解更多Lips & Boys系列详情。
            </h3>
            <div class="logo">
                <img src="../src/dist/images/logo.jpg" alt=""/>
            </div>
            <p class="des">
                <strong>北京</strong>
                北京市朝阳区建国路87号<br>
                新光SKP1楼<br>
                电话：+ 86 10 5738 1365<br>
                北京市西城区西单北大街176号<br>
                汉光百货1楼<br>
                电话：+ 86 10 6601 0366<br><br>

                <strong>上海</strong>
                上海市静安区南京西路1038号<br>
                梅龙镇伊势丹百货1楼<br>
                电话：+ 86 21 6217 8167<br>
                上海市黄浦区淮海中路99号<br>
                大上海时代广场连卡佛1楼<br>
                电话：+ 86 21 6013 0171<br><br>

                <strong>杭州</strong>
                杭州市下城区延安路530号<br>
                银泰百货武林总店1楼<br>
                电话：+ 86 571 8583 6282<br><br>

                <strong>成都</strong>
                成都市总府路15号<br>
                成都王府井1楼<br>
                电话：+ 86 28 8527 2768<br><br>

                <strong>昆明</strong>
                昆明市五华区三市街6号<br>
                联百盛1楼<br><br>

                <strong>天津</strong>
                天津市和平区南京路8号<br>
                伊势丹商场1楼
            </p>
        </div>

    </div>
</div>
</body>
</html>