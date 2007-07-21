/** JavaScript **/
ieHover = function() {
var ieULs = document.getElementById('nav').getElementsByTagName('ul');
/** IE script to cover <select> elements with <iframe>s **/
for (j=0; j<ieULs.length; j++) {
ieULs[j].innerHTML = ('<iframe src="about:blank" scrolling="no" frameborder="0"></iframe>' + ieULs[j].innerHTML);
/*ieULs[j].innerHTML = ('<iframe id="iePad' + j + '" src="about:blank" scrolling="no" frameborder="0" style=""></iframe>' + ieULs[j].innerHTML);
	var ieMat = document.getElementById('iePad' + j + '');*/
//	var ieMat = ieULs[j].childNodes[0];  alert(ieMat.nodeName); // also works...
	var ieMat = ieULs[j].firstChild;
		ieMat.style.width=ieULs[j].offsetWidth+"px";
		ieMat.style.height=ieULs[j].offsetHeight+"px";
		ieULs[j].style.zIndex="99";
}
}
if (window.attachEvent) window.attachEvent('onload', ieHover);
/** end **/