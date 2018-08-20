
////改变公共头部的内容
//function changeCommonHead(title){
//	var head=document.getElementById('commonHead');
//	head.innerText=title;
//}
////改变公共头部的内容
//function changeCommonFoot(j){
//	var str='_on.png';
//	var str1='.png';
//	var imgs=document.getElementsByClassName('footicon');
//	var foottitle=document.getElementsByClassName('foottitle');
//	for(var i=0;i<imgs.length;i++){
//		imgs[i].src=imgs[i].src.replace(str,str1);
//		foottitle[i].style.color='#000';
//	}
//	imgs[j].src=imgs[j].src.replace(str1,str);
//	foottitle[j].style.color='#0099d9';
//}

//function resetCount() {
//	if(window.localStorage) {
//      // 是否已经有记录了，如果有进入操作
//      if(window.localStorage.getItem("count")) {
//          //拿出key对应的value， 因为存储进去的是字符串。
//          var c = parseInt(window.localStorage.getItem("count"));
//          // 设置key，value加1
//          window.localStorage.setItem("count",0);
//          console.log(parseInt(window.localStorage.getItem("count")));
//      }else {
//          //如果没有检查到key, 那肯定没设置，那就让他默认为0
//          window.localStorage.setItem("count",0);
//          console.log(0);
//      }
//  }
//	
//}
//function count() {
//  // 当前浏览器是否支持localStorage
//  if(window.localStorage) {
//      // 是否已经有记录了，如果有进入操作
//      if(window.localStorage.getItem("count")) {
//          //拿出key对应的value， 因为存储进去的是字符串。
//          var c = parseInt(window.localStorage.getItem("count"));
//          // 设置key，value加1
//          window.localStorage.setItem("count",c+1);
//          console.log(parseInt(window.localStorage.getItem("count")));
//      }else {
//          //如果没有检查到key, 那肯定没设置，那就让他默认为0
//          window.localStorage.setItem("count",0);
//          console.log(0);
//      }
//  }
//}