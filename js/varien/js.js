function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function popWin(url,win,para) { window.open(url,win,para); }


function fieldset_highlight(obj,state) {
	for (var i=0, fieldset=obj.parentNode; i<10 && fieldset && fieldset.tagName!='FIELDSET'; i++, fieldset=fieldset.parentNode);
	if (fieldset && fieldset.tagName=='FIELDSET' && fieldset.className.indexOf('group-select')!=-1) {
		if (state) fieldset.className += ' highlight'; 
		else fieldset.className = fieldset.className.replace(/ highlight/,'');
	}
}


function fieldset_highlight_event(e) {
	if (!e) e = window.event;
	var obj = e.srcElement ? e.srcElement : e.target;
	var state = e.type=='focus';
	fieldset_highlight(obj,state);
}

function fieldset_init(fs) {
	var f = fs.getElementsByTagName('INPUT'), i;
	for (i=0; i<f.length; i++) {
		f[i].onfocus = fieldset_highlight_event;
		f[i].onblur = fieldset_highlight_event;
	}
	f = fs.getElementsByTagName('SELECT');
	for (i=0; i<f.length; i++) {
		f[i].onfocus = fieldset_highlight_event;
		f[i].onblur = fieldset_highlight_event;
	}
	f = fs.getElementsByTagName('TEXTAREA');
	for (i=0; i<f.length; i++) {
		f[i].onfocus = fieldset_highlight_event;
		f[i].onblur = fieldset_highlight_event;
	}
}

function fieldset_init_all() {
	var fs = document.getElementsByTagName('FIELDSET'), i;
	for (i=0; i<fs.length; i++) {
		fieldset_init(fs[i]);
	}
}

// Version 1.0
var isIE = navigator.appVersion.match(/MSIE/) == "MSIE";

if (!window.Varien)
    var Varien = new Object();

Varien.showLoading = function(){
    Element.show('loading-process');
}
Varien.hideLoading = function(){
    Element.hide('loading-process');
}
Varien.GlobalHandlers = {
    onCreate: function() {
        Varien.showLoading();
    },

    onComplete: function() {
        if(Ajax.activeRequestCount == 0) {
            Varien.hideLoading();
        }
    }
};

Ajax.Responders.register(Varien.GlobalHandlers);


Varien.CompareController = Class.create();

Varien.CompareController.prototype = {
	initialize: function(container, options) {
		// Default configuration values
		this.updateUrl = false;
		this.removeUrl = false;
		this.successMessage = false;
		this.removeMessage = false;
		this.confirmMessage = false;
		this.useAjax = true;
		this.container = $(container);
		if(options) {
			$H(options).each(function(pair) {
				if(typeof this[pair.key] != 'function' && pair.key != 'container') {
					if(pair.key == 'updateUrl' || pair.key == 'removeUrl') {
						this[pair.key] = new Template(pair.value);
					} else {
						this[pair.key] = pair.value;
					}
				}
			}.bind(this));
		}
	},
	addItem: function(id) {
		if(this.useAjax && this.container) {
			new Ajax.Updater(this.container, this.updateUrl.evaluate({id:id}) + '?ajax=1', {
				onComplete: function() {
					if(this.successMessage) {
						window.alert(this.successMessage);
					}
				}.bind(this)
			});

			if(this.container && this.container.getStyle('display')=='none') {
				this.container.show();
			}
		} else {
			window.location.href = this.updateUrl.evaluate({id:id});
		}
		
	},
	removeItem: function () {
		var item = arguments[0];
		var showMess = true;
		if(arguments[1]===false) {
			showMess = false;
		}
		if(!this.confirmMessage || !showMess || window.confirm(this.confirmMessage)) {
			
			var id = 0;
			var parentItem = false
			if(typeof item == 'object') {
				item	= $(item);
				parentItem = $(item.parentNode);
				if(parentItem.hasClassName('block-compare-item')) {
					id = parentItem.getElementsByClassName('compare-item-id')[0].value;
					parentItem.remove();
					if(this.container.getElementsByClassName('block-compare-item').length == 0) {
						this.container.hide();
					} else {
						var items = this.container.getElementsByClassName('block-compare-item');
						var lastItem = $(items[items.length-1]);
						if(!lastItem.hasClassName('last')) {
							lastItem.addClassName('last');
						}
					}
				} else {
					return;
				}
			} else {
				id = item;
			}
			
			if(this.useAjax) {
				var removeMessage = this.removeMessage;
				var container = this.container;
				new Ajax.Request(this.removeUrl.evaluate({id:id}) + '?ajax=1', {onComplete: function() {
					if(removeMessage && showMess) {
						window.alert(removeMessage);
					}
				}});
			} else {
				window.location.href = this.updateUrl.evaluate({id:id});
			}
		}
	},
	removeAll: function() {
		if(!this.confirmMessage || window.confirm(this.confirmMessage)) {
			var items = this.container.getElementsByClassName('block-compare-item');
			for(var i=0; i<items.length; i++) {
				if($(items[i]).getElementsByClassName('action').length==1) {
					this.removeItem($(items[i]).getElementsByClassName('action')[0], false);
				}
			}
		}
	}
}