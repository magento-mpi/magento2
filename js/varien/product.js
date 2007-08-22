

function openLargeImageWin(url, width, height)
{
	win = window.open(url,'largimage','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+width+',height='+height+',screenX=150,screenY=150,top=150,left=150');
	win.focus();
}

if(typeof Product=='undefined') {
	var Product = {};
}

/********************* IMAGE ZOOMER ***********************/

Product.Zoom = Class.create();
Product.Zoom.prototype = {
	initialize: function(imageEl, trackEl, handleEl){
		this.imageEl = $(imageEl);
		this.handleEl = $(handleEl);
		this.trackEl = $(trackEl);
		
		Event.observe(this.imageEl, 'dblclick', this.toggleFull.bind(this));
		
		this.image = new Draggable(imageEl, {
			starteffect:false, 
			reverteffect:false, 
			endeffect:false, 
			snap:this.containIt.bind(this)
		});
		this.slider = new Control.Slider(handleEl, trackEl, {
			axis:'horizontal', 
			minimum:0, 
			maximum:200, 
			alignX:0, 
			increment:1, 
			sliderValue:0, 
			onSlide:this.scaleIt.bind(this), 
			onChange:this.scaleIt.bind(this)
		});
		
		this.origWidth = 300;
		this.origHeight = 300;
		this.floorSize = 1.0;
		this.ceilingSize = 10.0;
		this.imageX = 0;
		this.imageY = 0;
		this.imageZoom = 1;
		this.showFull = false;
		this.selects = document.getElementsByTagName('select');
	},
		
	toggleFull: function () {
		this.showFull = !this.showFull;
		//TODO: hide selects for IE only
		for (i=0; i<this.selects.length; i++) {
			this.selects[i].style.visibility = this.showFull ? 'hidden' : 'visible';
		}
		this.trackEl.style.visibility = this.showFull ? 'hidden' : 'visible';
		this.imageEl.parentNode.style.overflow = this.showFull ? 'visible' : 'hidden';
	},
	
	scaleIt: function (v) {
		var centerX = (this.origWidth*(1-this.imageZoom)/2-this.imageX)/this.imageZoom;
		var centerY = (this.origHeight*(1-this.imageZoom)/2-this.imageY)/this.imageZoom;
		
		this.imageZoom = this.floorSize+(v*(this.ceilingSize-this.floorSize));
		
		this.imageEl.style.width = (this.imageZoom*this.origWidth)+'px';
		
		this.imageX = this.origWidth*(1-this.imageZoom)/2-centerX*this.imageZoom;
		this.imageY = this.origHeight*(1-this.imageZoom)/2-centerY*this.imageZoom;
		this.containIt(this.imageX, this.imageY, this.image);
		
		this.image.element.style.left = this.imageX+'px';
		this.image.element.style.top = this.imageY+'px';
	},

	containIt: function (x,y,draggable) {
		var pDim = Element.getDimensions(draggable.element.parentNode);
		var eDim = Element.getDimensions(draggable.element);
		var xMin = 0, xMax = pDim.width-eDim.width;
		var yMin = 0, yMax = pDim.height-eDim.height;
		x = x>xMin ? xMin : x;
		x = x<xMax ? xMax : x;
		y = y>yMin ? yMin : y;
		y = y<yMax ? yMax : y;
		this.imageX = x;
		this.imageY = y;
		return [x,y];
	}
}

/**************************** SUPER PRODUCTS ********************************/

Product.Super = {};
Product.Super.Configurable = Class.create();

Product.Super.Configurable.prototype = {
	initialize: function(container, observeCss, updateUrl, updatePriceUrl, priceContainerId) {
		this.container = $(container);
		this.observeCss = observeCss;
		this.updateUrl = updateUrl;
		this.updatePriceUrl = updatePriceUrl;
		this.priceContainerId = priceContainerId;
		this.registerObservers();
	},
	registerObservers: function() {
		var elements = this.container.getElementsByClassName(this.observeCss);
		elements.each(function(element){
			Event.observe(element, 'change', this.update.bindAsEventListener(this));
		}.bind(this));
		return this;
	},
	update: function(event) {
		var elements = this.container.getElementsByClassName(this.observeCss);
		var parameters = Form.serializeElements(elements, true);
		
		new Ajax.Updater(this.container, this.updateUrl + '?ajax=1', {
				parameters:parameters,
				onComplete:this.registerObservers.bind(this)
		});
		var priceContainer = $(this.priceContainerId);
		if(priceContainer) {
			new Ajax.Updater(priceContainer, this.updatePriceUrl + '?ajax=1', {
				parameters:parameters
			});
		}
	}
}