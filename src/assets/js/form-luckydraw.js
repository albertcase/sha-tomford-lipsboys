/*For join page
 * Inclue two function, one is load new qr for each person, another is show rule popup
 * */
;(function(){
    var controller = function(){
        //isSubmit  /*是否提交了用户详细信息表单*/
        //isLuckyDraw /*是否抽奖*/
        //remaintimes /*剩余抽奖次数*/
        this.disableClick = false;
        this.timeSlotJson = ['请选择一个时间段','10:00-10:30AM','10:30-11:30AM','11:30AM-12:30PM','12:30PM-13:30PM'];
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
        self.showTimeSlot();
        Common.gotoPin(0);
        //self.updateLuckyDrawStatus();
    };

    //bind Events
    controller.prototype.bindEvent = function(){
        var self = this;
        /*
         * submit the form
         * */
        $('.btn-submit').on('touchstart',function(){
            if(self.validateForm()){
                //name mobile province city area address
                var inputNameVal = $('#input-name').val(),
                    inputMobileVal = $('#input-mobile').val(),
                    inputMsgCodeVal = $('#input-validate-message-code').val(),
                    selectTimeSlotVal = $('#select-timeslot').val();
                Api.submitForm_apply({
                    name:inputNameVal,
                    mobile:inputMobileVal,
                    timeslot:selectTimeSlotVal,
                    msgCode:inputMsgCodeVal
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
        $('#select-timeslot').on('change',function(){
            //curProvinceIndex = document.getElementById('select-timeslot').selectedIndex;
            $('#input-text-timeslot').val($(this).val());
        });

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
     * Countdown
     * Disabled click the button untill the end the countdown
     * */
    controller.prototype.countDown = function(){
        var self = this;
        self.disableClick = true;
        $('.btn-get-msg-code').addClass('disabled');
        var maxSeconds = 60;
        var ele = $('.btn-get-msg-code .tt');
        var aaa = setInterval(function(){
            maxSeconds--;
            ele.text(''+maxSeconds+'s'+'');
            if(maxSeconds<1){
                self.disableClick = false;
                ele.text('获取验证码');
                $('.btn-get-msg-code').removeClass('disabled');
                clearInterval(aaa);
            }
        },1000);
    };


    //province city and district
    controller.prototype.showTimeSlot = function(){
        var self = this;
        //    list all province
        var timeSlots = '';
        var timeslotSelectEle = $('#select-timeslot'),
            provinceInputEle = $('#input-text-province');
        self.timeSlotJson.forEach(function(item){
            timeSlots = timeSlots+'<option value="'+item+'">'+item+'</option>';
        });
        timeslotSelectEle.html(timeSlots);
        //provinceInputEle.val(provinceSelectEle.val());
        //self.showCity(0);
        //self.showDistrict(0,0);
    };

    //validation the form
    controller.prototype.validateForm = function(){
        var self = this;
        var validate = true,
            inputName = document.getElementById('input-name'),
            inputMobile = document.getElementById('input-mobile'),
            inputValidateCode = document.getElementById('input-validate-message-code'),
            selectTimeSlot = document.getElementById('select-timeslot');

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

        if(!selectTimeSlot.value || selectTimeSlot.value == self.timeSlotJson[0]){
            //Common.errorMsg.add(selectProvince.parentElement,'请选择省份');
            Common.errorMsgBox.add('请选择一个时间段');
            validate = false;
        }else{
            //Common.errorMsg.remove(selectProvince.parentElement);
        };

        if(!inputValidateCode.value){
            //Common.errorMsg.add(selectProvince.parentElement,'请选择省份');
            Common.errorMsgBox.add('请输入短信验证码');
            validate = false;
        }else{
            //Common.errorMsg.remove(selectProvince.parentElement);
        };

        if(validate){
            return true;
        }
        return false;
    };

    $(document).ready(function(){
//    show form
        var newFollow = new controller();
        newFollow.startUp();

    });

})();