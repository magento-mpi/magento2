
ieHover = function() {
	var ieULs = $('nav').getElementsByTagName('ul'), iframe, li;
	for (var j=0; j<ieULs.length; j++) {
		iframe = document.createElement('IFRAME');
		iframe.src = "about:blank";
		iframe.scrolling = 'no';
		iframe.frameBorder = 0;
		iframe.style.width = ieULs[j].offsetWidth+"px";
		iframe.style.height = ieULs[j].offsetHeight+"px";
		ieULs[j].insertBefore(iframe, ieULs[j].firstChild);
		ieULs[j].style.zIndex="1";
	}
}

Event.observe(window, 'load', ieHover);