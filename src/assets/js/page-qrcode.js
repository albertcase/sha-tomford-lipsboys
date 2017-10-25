$(document).ready(function(){
    var curCode = Common.getParameterByName('code');
    //generate qrcode
    new QRCode(document.getElementById('generate-qrcode'), window.location.origin+'/consume?code='+curCode);

});