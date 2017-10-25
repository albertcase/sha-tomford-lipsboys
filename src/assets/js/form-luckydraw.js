/*For join page
 * Inclue two function, one is load new qr for each person, another is show rule popup
 * */
;(function(){
    var controller = function(){
        this.disableClick = false;
        this.reservationDate = '';
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

        // if user has reservated success, to confirm page
        // if user has not success, but maxNumber, to form page
        // if user has not success, and maxNumber is 0, go shop list page
        if(isReservation){
            Common.gotoPin(1);
        }else{
            if(maxNumber){
                self.showTimeSlot();
                Common.gotoPin(0);
            }else{
                Common.gotoPin(2);
            }
        }
    };

    //bind Events
    controller.prototype.bindEvent = function(){
        var self = this;

        Common.overscroll(document.querySelector('#pin-shoplists'));
        Common.overscroll(document.querySelector('#pin-fillform'));

        /*
         * submit the form
         * */
        $('.btn-submit').on('touchstart',function(){
            _hmt.push(['_trackEvent', 'btn', 'click', 'submitForm']);
            if(self.validateForm()){
                //name mobile province city area address
                var inputNameVal = $('#input-name').val(),
                    inputMobileVal = $('#input-mobile').val(),
                    inputMsgCodeVal = $('#input-validate-message-code').val(),
                    selectTimeSlotVal = $('#select-timeslot').val();
                Api.checkMsgValidateCode({
                    phone: inputMobileVal,
                    phonecode: inputMsgCodeVal
                },function(json){
                    if(json.status==1){
                    //    if validate message is right, then submit
                        Api.submitForm_apply({
                            name:inputNameVal,
                            phone:inputMobileVal,
                            timeslot:selectTimeSlotVal,
                            //msgCode:inputMsgCodeVal
                        },function(data){
                            if(data.status==1){
                                //go success page
                                //self.reservationDate = selectTimeSlotVal;
                                $('.details .date').html(selectTimeSlotVal);
                                Common.gotoPin(1);
                            }else{
                                Common.alertBox.add(data.msg);
                            }
                        });
                    }else{
                        Common.alertBox.add(json.msg);
                    }
                });

            }

        });

        //    switch the province
        var curProvinceIndex = 0;
        $('#select-timeslot').on('change',function(){
            var curIndex = document.getElementById('select-timeslot').selectedIndex;
            var curPeopleNum =  $('#select-timeslot option').eq(curIndex).attr('data-num');
            //test number
            //curPeopleNum  = 200;
            if(curPeopleNum<1){
                Common.alertBox.add('此时段预约人数已经满额');
                return;
            }
            $('#input-text-timeslot').val($(this).val());

        });


        /*
         * validate phonenumber first
         * Get message validate code,check image validate code
         * if image validate code is right
         * */
        $('.btn-get-msg-code').on('touchstart', function(){
            _hmt.push(['_trackEvent', 'btn', 'click', 'validateCode']);
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
                ele.text('验证');
                $('.btn-get-msg-code').removeClass('disabled');
                clearInterval(aaa);
            }
        },1000);
    };


    //province city and district
    controller.prototype.showTimeSlot = function(){
        var self = this;
        //    list all province
        var timeSlots = '<option value="">选择时段</option>';
        var timeslotSelectEle = $('#select-timeslot');
        //get all apply list
        Api.getApplyList(function(json){
            if(json.status==1){
                json.data.forEach(function(item){
                    timeSlots = timeSlots+'<option value="'+item.name+'" data-num="'+item.num+'">'+item.name+'</option>';
                });
                timeslotSelectEle.html(timeSlots);
            }else{
                Common.alertBox.add(json.msg);
            }
        });
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

        if(!selectTimeSlot.value){
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