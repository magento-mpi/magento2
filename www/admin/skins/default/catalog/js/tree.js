var VarienTree		= Class.create();

// Varien Tree Widget Prototype
VarienTree.prototype = {
	initialize: function (element){
		this.id = element;
		this.isDragMode = false;
		this.element = $(this.id);
		this.eventOnClick	= this.treeToggle.bindAsEventListener(this);
		this.eventOnFocus	= this.onFocus.bindAsEventListener(this);
		this.eventOnBlur	= this.onBlur.bindAsEventListener(this);
	},

	init: function () {
		this.convert(this.element);
	},

	convert: function (ul){
		var j = 0;
		var lastNode = null;
		while (j < ul.childNodes.length) {
			if (ul.childNodes[j].ELEMENT_NODE == ul.childNodes[j].nodeType) {
				if (ul.childNodes[j].tagName && ul.childNodes[j].tagName.toLowerCase() == 'li') {
					lastNode = ul.childNodes[j];
					this.cover(ul.childNodes[j]);
				}
			}
			j++;
		}
		lastNode.addClassName('last');
	},

	cover: function (li) {
		var j = 0;
		var spanA = spanB = spanC = null;
		var spanOn = false;
		if (li.getAttribute('left') != 1) {
			new Draggable(li,{revert:true});
		}

//		new Draggable(li,{revert:true,starteffect:false});
		while (j < li.childNodes.length) {
			if (li.childNodes[j].tagName && li.childNodes[j].tagName.toLowerCase() == 'ul') {
				spanA.addClassName('children');
				li.childNodes[j-1].addClassName('children');
				this.convert(li.childNodes[j]);
				spanOn = false;
			} else {
				if (spanOn == false) {
					spanA = document.createElement('span');
					spanB = document.createElement('span');
					spanC = document.createElement('span');

					spanA.appendChild(spanB);
					spanB.appendChild(spanC);
					spanA.className = 'a ' + li.className.replace('closed','spanClosed');
					spanA.onMouseOver = function() {};
					spanB.className = 'b';
  				    Event.observe(spanB, 'click', this.eventOnClick);					
					spanC.className = 'c';
					Event.observe(spanC, 'focus', this.eventOnFocus);
					Event.observe(spanC, 'blur', this.eventOnBlur);
					
					li.insertBefore(spanA, li.childNodes[j]);
					spanC.appendChild(li.childNodes[j+1]);	
					spanOn = true;
				} else {
					spanC.appendChild(li.childNodes[j]);
					j--;
				}
			}
			j++;
		}
		spanA.addClassName('last');	
	},

	treeToggle: function(event) {
		var span = Event.findElement(event, 'span');
		if (span.match('span.c')) { // check css selector element#id.classname
			return true;
		};
		var li = Event.findElement(event, 'li');
		li.toggleClassName('closed');
		span.parentNode.toggleClassName('spanClosed');
		return true;
	},
	
	
	onFocus: function(event) {
		var a = Event.findElement(event, 'a');
		a.addClassName('active');
	},
	
	onBlur: function(event) {
		var a = Event.findElement(event, 'span');
		a.removeClassName('active');
//		var span = Event.findElement(event, 'span');
//		if (span.match('span.c')) { // check css selector element#id.classname
//			span.removeClassName('active');
//		};
	},

	onchangeReload:function (obj){
	},

	_getNearRow: function (where){
	},

	registerActiveRow: function (row){
	},
	dataMouseOver: function (event){
	},

	dataMouseOut: function (event){
	},

	dataClick: function (event){
	},

	dataDblClick: function (event){
	},

	dataKeyPress: function (event){
		if (this.activeRow && event.keyCode)
		{
			//alert(event.keyCode);
			switch (event.keyCode)
			{
				case Event.KEY_DOWN:
//				case Event.KEY_RIGHT:
					var element = this._getNearRow('after');
					break;
				case Event.KEY_UP:
//				case Event.KEY_LEFT:
					var element = this._getNearRow('before');
					break;
				// PgUp and Home
/*				case 33:
				case 36:
					var element = this._getNearRow('first');
					break;
				// PgDn and End
				case 34:
				case 35:
					var element = this._getNearRow('end');
					break;
				case Event.KEY_RETURN:
					var element = Event.findElement(event, 'tr');
					alert(this.element.id);
					Event.stop(event);
					break;*/
				default:
					var element = false;
			}

			if(element)
			{
				this.registerActiveRow(element);
				Element.scrollTo(this.activeRow);
				Event.stop(event);
			}
			return true;
		}
	},

    getBaseUrl: function (){
        var url = this.baseUrl;
        if (this.baseUrl.indexOf('?') > -1)
        {
            url+= '&';
        }
        else
        {
        	url+= '?';
        }

        return url;
    }
}