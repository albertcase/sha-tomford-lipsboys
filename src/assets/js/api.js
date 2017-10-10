/*All the api collection*/
Api = {
    //submit user info
    //{
    //    name: '张三',
    //        tel: '13112345678',
    //    province: '上海',
    //    city: '上海',
    //    area: '黄浦区',
    //    address: '湖滨路'
    //}
    //Form submit of the freetrial

    //Form submit of the luckydraw
    submitForm_apply:function(obj,callback){
        Common.msgBox.add('loading...');
        $.ajax({
            url:'/api/apply',
            type:'POST',
            dataType:'json',
            data:obj,
            success:function(data){
                Common.msgBox.remove();
                return callback(data);
                //status=1 有库存
            }
        });

        //return callback({
        //    status:0,
        //    msg:'fillform'
        //})


    },

    //Get reservation lists
    getApplyList:function(obj,callback){
        Common.msgBox.add('loading...');
        $.ajax({
            url:'/api/applylist',
            type:'POST',
            dataType:'json',
            data:obj,
            success:function(data){
                Common.msgBox.remove();
                return callback(data);
                //status=1 有库存
            }
        });
    },


    //sent message validate code
    //mobile
    sendMsgValidateCode:function(obj,callback){
        Common.msgBox.add('loading...');
        $.ajax({
            url:'/api/phonecode',
            type:'POST',
            dataType:'json',
            data:obj,
            success:function(data){
                Common.msgBox.remove();
                return callback(data);
            }
        });

        //return callback({
        //    status:1,
        //    msg:'提交成功'
        //});


    },

    //check message validate code
    checkMsgValidateCode:function(obj,callback){
        Common.msgBox.add('loading...');
        $.ajax({
            url:'/api/checkphonecode',
            type:'POST',
            dataType:'json',
            data:obj,
            success:function(data){
                Common.msgBox.remove();
                return callback(data);
            }
        });

        //return callback({
        //    status:1,
        //    msg:'提交成功'
        //});


    },


};