dropdown = function() {
	var ele = document.getElementById("nav").getElementsByTagName("LI");
	for (var i=0; i<ele.length; i++) {
		ele[i].onmouseover=function() {
			this.className+=" over";
		}
		ele[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" over\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", dropdown);