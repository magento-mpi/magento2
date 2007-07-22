
ieHover = function() {
	var ieULs = ($'nav').getElementsByTagName('ul');
	for (j=0; j<ieULs.length; j++) {
		ieULs[j].innerHTML = ('<iframe src="about:blank" scrolling="no" frameborder="0"></iframe>' + ieULs[j].innerHTML);
		var ieMat = ieULs[j].firstChild;
		ieMat.style.width=ieULs[j].offsetWidth+"px";
		ieMat.style.height=ieULs[j].offsetHeight+"px";
		ieULs[j].style.zIndex="99";
	}
}

Event.observe(window, 'load', ieHover);
