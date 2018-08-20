var u = navigator.userAgent; //判断什么类型的手机
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

if(isiOS==true){
	$(".main").css("margin-top","70px");
}

