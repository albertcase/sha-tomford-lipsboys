/*For join page
 * Inclue two function, one is load new qr for each person, another is show rule popup
 * */
;(function(){
    var controller = function(){
        //isSubmit  /*是否提交了用户详细信息表单*/
        //isLuckyDraw /*是否抽奖*/
        //remaintimes /*剩余抽奖次数*/
        this.user = userInfo;
        this.disableClick = false;
        this.enableTotalTimes = 1; //说明如果满1次抽奖机会就有权限抽奖了因为是小于号
    };
    //init
    controller.prototype.init = function(){
        var self = this;

        var timeStart = 0,
            step= 1,
            isTrueNext = false,
            isFalseNext = false;
        var loadingAni = setInterval(function(){
            if(timeStart>100){
                isFalseNext = true;
                if(isTrueNext){
                    self.startUp();
                }
                clearInterval(loadingAni);
                return;
            };
            if(timeStart==step){
                $('.animate-flower').addClass('fadenow');
            }
            $('.loading-num .num').html(timeStart);
            timeStart += step;
        },50);

        var baseurl = ''+'/src/dist/images/';
        var imagesArray = [
            baseurl + 'preload-flower.jpg',
            baseurl + 'preload-bg.jpg',
            baseurl + 'logo.png',
            baseurl + 'ani-1.png',
            baseurl + 'ani-2.png',
            baseurl + 'ani-3.png',
            baseurl + 'ani-5.png',
            baseurl + 'bg.jpg',
            baseurl + 'btn.png',
            baseurl + 'tag-new.png',
            baseurl + 'f-1.png',
            baseurl + 'fleurs-2.png',
            baseurl + 'fleurs.png',
            baseurl + 'foreground-1.png',
            baseurl + 'gift-flower.png',
            baseurl + 'guide-share.png',
            baseurl + 'landing-1.png',
            baseurl + 'pop-bg.png',
            baseurl + 'text.png'
        ];

        var i = 0,j= 0;
        new preLoader(imagesArray, {
            onProgress: function(){
                i++;
                //var progress = parseInt(i/imagesArray.length*100);
                //console.log(progress);
                //$('.preload .v-content').html(''+progress+'%');
                //console.log(i+'i');
            },
            onComplete: function(){
                isTrueNext  = true;
                if(isFalseNext){
                    self.startUp();
                }

            }
        });


    };

    controller.prototype.startUp = function(){
        var self = this;
        $('.preload').remove();
        $('.wrapper').addClass('fade');
        self.bindEvent();
        self.showAllProvince();
        Common.gotoPin(0);
        //self.updateLuckyDrawStatus();
    };

    controller.prototype.updateLuckyDrawStatus = function(){
        var self = this;
        Api.luckydrawstatus(function(data){
            self.user.remaintimes = data.msg.remaintimes;
            if(data.status==1){
                if(self.user.isLuckyDraw || !data.msg.remaintimes){
                    $('.btn-start-luckydraw').addClass('disabled');
                };
                if(data.msg.totaltimes<self.enableTotalTimes){
                    //不符合抽奖条件,直接显示提示信息
                    Common.gotoPin(3);
                }else{
                    //显示首页的状态
                    self.loadHomePage();
                }
                $('.totaldays').html(data.msg.totaldays);
                $('.totaltimes').html(data.msg.totaltimes);
                $('.remaintimes').html(data.msg.remaintimes);
            }
        });
    };

    controller.prototype.loadHomePage = function(){
        var self = this;
        /*
         * status1: If the user wins the lottery, but not filled the details form, we need guide them to fill form;
         * status2: If the user wins the lottery, and filled form, show result page;
         * status3: If the user fails the win, but still has remain times, continue;
         * status4: If the user fails the win, and also no chance, we will show the sorry message
         * */
        if(self.user.isLuckyDraw && !self.user.isSubmit){
            Common.gotoPin(0);
            self.lotteryPop('popup-result-yes','恭喜您','获得KENZO花颜舒柔夜间修护面膜（75ML）一份！'+'<div class="btn btn-goinfo">'+'<span class="tt">填写寄送信息</span>'+'</div>');

            // test
            //Common.gotoPin(1);
            //self.lotteryPop('popup-result-yes','恭喜您','获得XXX一份！'+'<div class="btn btn-goinfo">'+'<span class="tt">填写寄送信息</span>'+'</div>');
        }else if(self.user.isLuckyDraw && self.user.isSubmit){
            Common.gotoPin(2);
        }else if(!self.user.isLuckyDraw && self.user.remaintimes){
            Common.gotoPin(0);
        }else if(!self.user.isLuckyDraw && !self.user.remaintimes){
            //很遗憾，您没有中奖！
            Common.gotoPin(0);
            $('.lucky-info').html('很遗憾，您没有中奖！');
            self.lotteryPop('popup-result-no','很遗憾，您没有中奖','请持续关注KENZO官方微信，'+'<br>'+'更多福利等着你哦！');
        }
    }

    //bind Events
    controller.prototype.bindEvent = function(){
        var self = this;

        /*remove the pop lottery result*/
        $('body').on('touchstart', '.pop-lottery-result .btn-close',function(){
            $('.pop-lottery-result').remove();
        });

        /*Show link-terms popup*/
        $('.link-terms').on('touchstart', function(){
            $('.terms-pop').addClass('show');
        });

        /*
        * Hide link terms pop
        * */
        $('.terms-pop .btn-close').on('touchstart', function(){
            $('.terms-pop').removeClass('show');
        });

        /*
        * Start lottery
        * */
        $('.btn-start-luckydraw').on('touchstart', function(){
            _hmt.push(['_trackEvent', 'button', 'click', 'StartToLuckyDraw']);
            if($('.btn-start-luckydraw').hasClass('disabled')) return;
            Api.lottery(function(data){
                //self.updateLuckyDrawStatus();
                switch (data.status){
                    case 0:
                        //msg: '遗憾未中奖',
                        Api.luckydrawstatus(function(json){
                            self.user.remaintimes = json.msg.remaintimes;
                            if(json.status==1){
                                if(self.user.isLuckyDraw || !json.msg.remaintimes){
                                    $('.btn-start-luckydraw').addClass('disabled');
                                };
                                if(!self.user.remaintimes){
                                    $('.lucky-info').html('很遗憾，您没有中奖！');
                                    self.lotteryPop('popup-result-no','很遗憾，您没有中奖','请持续关注KENZO官方微信，'+'<br>'+'更多福利等着你哦！');
                                }else{
                                    $('.lucky-info').html('很遗憾，您没有中奖！<br>再次点击“抽奖”试试看吧！');
                                }
                                $('.remaintimes').html(json.msg.remaintimes);
                            }else{
                                Common.alertBox.add(json.msg);
                            }
                        });
                        break;
                    case 1:
                        //msg: '恭喜中奖'
                        self.lotteryPop('popup-result-yes','恭喜您','获得KENZO花颜舒柔夜间修护面膜（75ML）一份！'+'<div class="btn btn-goinfo">'+'<span class="tt">填写寄送信息</span>'+'</div>');
                        break;
                    case 2:
                        //msg: '今天的奖品已经发没！',
                        Api.luckydrawstatus(function(json){
                            self.user.remaintimes = json.msg.remaintimes;
                            if(json.status==1){
                                if(self.user.isLuckyDraw || !json.msg.remaintimes){
                                    $('.btn-start-luckydraw').addClass('disabled');
                                };
                                if(!self.user.remaintimes){
                                    $('.lucky-info').html('很遗憾，您没有中奖！');
                                    self.lotteryPop('popup-result-no','很遗憾，您没有中奖','请持续关注KENZO官方微信，'+'<br>'+'更多福利等着你哦！');
                                }else{
                                    $('.lucky-info').html('很遗憾，您没有中奖！<br>再次点击“抽奖”试试看吧！');
                                }
                                $('.remaintimes').html(json.msg.remaintimes);
                            }else{
                                Common.alertBox.add(json.msg);
                            }
                        });
                        //self.lotteryPop('popup-result-no','很遗憾，您没有中奖','请持续关注KENZO官方微信，'+'<br>'+'更多福利等着你哦！');
                        break;
                    case 3:
                        //msg: '您已获奖',
                        self.lotteryPop('popup-result-yes','恭喜您','获得KENZO花颜舒柔夜间修护面膜（75ML）一份！'+'<div class="btn btn-goinfo">'+'<span class="tt">填写寄送信息</span>'+'</div>');
                        break;
                    default :
                        Common.alertBox.add(data.msg);
                }
            });
        });

        /*
        * Go form info page
        * */
        $('body').on('touchstart', '.btn-goinfo', function(){
            self.loadFormPage();
            $('.pop-lottery-result').remove();
        });

        /*
         * submit the form of luckydraw
         * if isTransformedOld is true, submit it and then call lottery api
         * if isTransformedOld is false, submit it and then call gift api
         * */
        $('.btn-submit').on('touchstart',function(){
            _hmt.push(['_trackEvent', 'button', 'click', 'submitLuckyDrawForm']);
            if(self.validateForm()){
                //name mobile province city area address
                var inputNameVal = $('#input-name').val(),
                    inputMobileVal = $('#input-mobile').val(),
                    inputAddressVal = $('#input-address').val(),
                    inputMsgCodeVal = $('#input-validate-message-code').val(),
                    selectProvinceVal = $('#select-province').val(),
                    selectCityVal = $('#select-city').val(),
                    selectDistrictVal = $('#select-district').val();
                Api.submitForm_luckydraw({
                    name:inputNameVal,
                    mobile:inputMobileVal,
                    province:selectProvinceVal,
                    city:selectCityVal,
                    msgCode:inputMsgCodeVal,
                    area:selectDistrictVal,
                    address:inputAddressVal
                },function(data){
                    if(data.status==1){
                        Common.gotoPin(2);
                    }else{
                        Common.alertBox.add(data.msg);
                    }
                });
            }

        });

        //    switch the province
        var curProvinceIndex = 0;
        $('#select-province').on('change',function(){
            curProvinceIndex = document.getElementById('select-province').selectedIndex;
            self.showCity(curProvinceIndex);
        });

        $('#select-city').on('change',function(){
            var curCityIndex = document.getElementById('select-city').selectedIndex;
            self.showDistrict(curProvinceIndex,curCityIndex);
        });

        $('#select-district').on('change',function(){
            var districtInputEle = $('#input-text-district'),
                districtSelectEle = $('#select-district');
            var curCityIndex = document.getElementById('select-district').selectedIndex;
            districtInputEle.val(districtSelectEle.val());
        });


        //    imitate share function on pc====test
        //    $('.share-popup .guide-share').on('touchstart',function(){
        //        self.shareSuccess();
        //    });

        //switch validate code
        $('.validate-code').on('touchstart', function(){
            self.getValidateCode();
        });

        /*
         * validate phonenumber first
         * Get message validate code,check image validate code
         * if image validate code is right
         * */
        $('.btn-get-msg-code').on('touchstart', function(){
            if(self.disableClick) return;
            if(!$('#input-mobile').val()){
                Common.errorMsgBox.add('手机号码不能为空');
            }else{
                var reg=/^1\d{10}$/;
                if(!(reg.test($('#input-mobile').val()))){
                    validate = false;
                    Common.errorMsgBox.add('手机号格式错误，请重新输入');
                }else{
                    if(!$('#input-validate-code').val()){
                        Common.alertBox.add('你的验证码不能为空');
                        return;
                    }
                    Api.checkImgValidateCode({
                        picture:$('#input-validate-code').val()
                    },function(data){
                        if(data.status == 1){
                            //start to count down and sent message to your phone
                            Api.sendMsgValidateCode({
                                mobile:$('#input-mobile').val()
                            },function(json){
                                if(json.status==1){
                                    //console.log('开始倒计时');
                                    self.countDown();
                                    self.disableClick = true;
                                }else{
                                    Common.alertBox.add(json.msg);
                                }
                            });
                        }else{
                            Common.alertBox.add('验证码输入错误，请重新输入');
                            self.getValidateCode();
                        }
                    });
                }
            }

        });


        /*
         * For share tips overlay,click will disappear
         * */
        $('.share-popup').on('touchstart', function(e){
            if(e.target.className.indexOf('.share-popup')){
                $('.share-popup').removeClass('show');
            }
        });

    };


    /*
    * Lottery result popup
    * */
    controller.prototype.lotteryPop = function(id,title,des){
        var lotteryHtml = '<div class="popup pop-lottery-result show" id="'+id+'">'+
            '<div class="inner">'+
            '<div class="f-2"></div>'+
            '<div class="msg">'+
            '<div class="f-1"></div>'+
            '<div class="f-3"></div>'+
            '<div class="result-content">'+
            '<h3 class="subtitle">'+
            '<span>'+title+'</span>'+
            '</h3>'+
            '<div class="des">'+des+'</div>'+
            '</div>'+
            '</div>'+
            '<div class="btn-close">关闭</div>'+
            '</div>'+
            '</div>';
        $('body').append(lotteryHtml);
    };

    /*
     * Countdown
     * Disabled click the button untill the end the countdown
     * */
    controller.prototype.countDown = function(){
        var self = this;
        self.disableClick = true;
        $('.btn-get-msg-code').addClass('disabled');
        var maxSeconds = 60;
        var ele = $('.btn-get-msg-code .second');
        var aaa = setInterval(function(){
            maxSeconds--;
            ele.text('('+maxSeconds+'s'+')');
            if(maxSeconds<1){
                self.disableClick = false;
                ele.text('');
                $('.btn-get-msg-code').removeClass('disabled');
                clearInterval(aaa);
            }
        },1000);
    };

    controller.prototype.getValidateCode = function(){
        Api.getImgValidateCode(function(data){
            //console.log(data);
            if(data.status==1){
                $('.validate-code-img').html('<img src="data:image/jpeg;base64,'+data.picture+'" />');
                //var codeImg = new Image();
                //codeImg.onload = function(){
                //
                //}
                //codeImg.src = data.picture;
            }
        });
    };


    //province city and district
    controller.prototype.showAllProvince = function(){
        var self = this;
        //    list all province
        var provinces = '';
        var provinceSelectEle = $('#select-province'),
            provinceInputEle = $('#input-text-province');
        region.forEach(function(item){
            provinces = provinces+'<option value="'+item.name+'">'+item.name+'</option>';
        });
        provinceSelectEle.html(provinces);
        provinceInputEle.val(provinceSelectEle.val());
        self.showCity(0);
        self.showDistrict(0,0);
    };

    controller.prototype.showCity = function(curProvinceId){
        var self = this;
        //    show current cities
        var cities='';
        var provinceSelectEle = $('#select-province'),
            provinceInputEle = $('#input-text-province'),
            citySelectEle = $('#select-city'),
            cityInputEle = $('#input-text-city');
        var cityJson = region[curProvinceId].city;
        cityJson.forEach(function(item,index){
            cities = cities + '<option data-id="'+index+'" value="'+item.name+'">'+item.name+'</option>';
        });
        citySelectEle.html(cities);
        provinceInputEle.val(provinceSelectEle.val());
        cityInputEle.val(citySelectEle.val());
        self.showDistrict(curProvinceId,0);
    };

    controller.prototype.showDistrict = function(curProvinceId,curCityId){
        var self = this;
        var districtSelectEle = $('#select-district'),
            districtInputEle = $('#input-text-district'),
            citySelectEle = $('#select-city'),
            cityInputEle = $('#input-text-city');
        //    show current districts
        var districts = '';
        var districtJson = region[curProvinceId].city[curCityId].area;
        districtJson.forEach(function(item,index){
            districts = districts + '<option data-id="'+index+'" value="'+item+'">'+item+'</option>';
        });
        cityInputEle.val(citySelectEle.val());
        districtSelectEle.html(districts);
        districtInputEle.val(districtSelectEle.val());
    };

    //validation the form
    controller.prototype.validateForm = function(){
        var self = this;
        var validate = true,
            inputName = document.getElementById('input-name'),
            inputMobile = document.getElementById('input-mobile'),
            inputAddress = document.getElementById('input-address'),
            selectProvince = document.getElementById('select-province'),
            selectCity = document.getElementById('select-city'),
            selectDistrict = document.getElementById('select-district');

        if(!inputName.value){
            Common.errorMsgBox.add('请填写姓名');
            validate = false;
        };

        if(!inputMobile.value){
            Common.errorMsgBox.add('手机号码不能为空');
            //Common.errorMsg.add(inputMobile.parentElement,'手机号码不能为空');
            validate = false;
        }else{
            var reg=/^1\d{10}$/;
            if(!(reg.test(inputMobile.value))){
                validate = false;
                Common.errorMsgBox.add('手机号格式错误，请重新输入');
                //Common.errorMsg.add(inputMobile.parentElement,'手机号格式错误，请重新输入');
            }else{
                //Common.errorMsg.remove(inputMobile.parentElement);
            }
        }

        if(!selectProvince.value || selectProvince.value == '省份'){
            //Common.errorMsg.add(selectProvince.parentElement,'请选择省份');
            Common.errorMsgBox.add('请选择省份');
            validate = false;
        }else{
            //Common.errorMsg.remove(selectProvince.parentElement);
        };

        if(!selectCity.value || selectCity.value == '城市' || !selectDistrict.value || selectDistrict.value == '区县' ){
            //Common.errorMsg.add(selectCity.parentElement.parentElement,'请选择城市和区县');
            Common.errorMsgBox.add('请选择城市和区县');
            validate = false;
        }else{
            //Common.errorMsg.remove(selectCity.parentElement);
        };

        if(!inputAddress.value){
            //Common.errorMsg.add(inputAddress.parentElement,'请填写地址');
            Common.errorMsgBox.add('请填写地址');
            validate = false;
        }else{
            //Common.errorMsg.remove(inputAddress.parentElement);
        };

        if(validate){
            return true;
        }
        return false;
    };
    controller.prototype.loadFormPage = function(){
        var self = this;
        self.getValidateCode();
        Common.gotoPin(1);

    };


    $(document).ready(function(){
//    show form
        var newFollow = new controller();
        newFollow.startUp();

    });

})();