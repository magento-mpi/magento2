

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
/**
 * Image zoom control
 * 
 * @author Moshe Gurvich <moshe@varien.com>
 */
Product.Zoom.prototype = {
	initialize: function(imageEl, trackEl, handleEl, zoomInEl, zoomOutEl){
		this.containerEl = $(imageEl).parentNode;
		this.imageEl = $(imageEl);
		this.handleEl = $(handleEl);
		this.trackEl = $(trackEl);
		
		this.containerDim = Element.getDimensions(this.containerEl);
		this.imageDim = Element.getDimensions(this.imageEl);
		this.imageDim.ratio = this.imageDim.width/this.imageDim.height;
		
		this.floorZoom = 1;
		this.ceilingZoom = this.imageDim.width/this.containerDim.width;
		this.imageX = 0;
		this.imageY = 0;
		this.imageZoom = 1;
		
		this.sliderSpeed = 0;
		this.sliderAccel = 0;
		this.zoomBtnPressed = false;
		
		this.showFull = false;
		
		this.selects = document.getElementsByTagName('select');
		
		this.draggable = new Draggable(imageEl, {
			starteffect:false, 
			reverteffect:false, 
			endeffect:false, 
			snap:this.contain.bind(this)
		});
		
		this.slider = new Control.Slider(handleEl, trackEl, {
			axis:'horizontal', 
			minimum:0, 
			maximum:Element.getDimensions(this.trackEl).width, 
			alignX:0, 
			increment:1, 
			sliderValue:0, 
			onSlide:this.scale.bind(this), 
			onChange:this.scale.bind(this)
		});

		this.scale(0);
		
		Event.observe(this.imageEl, 'dblclick', this.toggleFull.bind(this));
		
		Event.observe($(zoomInEl), 'mousedown', this.startZoomIn.bind(this));
		Event.observe($(zoomInEl), 'mouseup', this.stopZooming.bind(this));
		Event.observe($(zoomInEl), 'mouseout', this.stopZooming.bind(this));
		
		Event.observe($(zoomOutEl), 'mousedown', this.startZoomOut.bind(this));
		Event.observe($(zoomOutEl), 'mouseup', this.stopZooming.bind(this));	
		Event.observe($(zoomOutEl), 'mouseout', this.stopZooming.bind(this));	
	},
		
	toggleFull: function () {
		this.showFull = !this.showFull;
		//TODO: hide selects for IE only
		for (i=0; i<this.selects.length; i++) {
			this.selects[i].style.visibility = this.showFull ? 'hidden' : 'visible';
		}
		this.trackEl.style.visibility = this.showFull ? 'hidden' : 'visible';
		this.containerEl.style.overflow = this.showFull ? 'visible' : 'hidden';
		
		return this;
	},
	
	scale: function (v) {
		var centerX = (this.containerDim.width*(1-this.imageZoom)/2-this.imageX)/this.imageZoom;
		var centerY = (this.containerDim.height*(1-this.imageZoom)/2-this.imageY)/this.imageZoom;
		
		this.imageZoom = this.floorZoom+(v*(this.ceilingZoom-this.floorZoom));
		
		this.imageEl.style.width = (this.imageZoom*this.containerDim.width)+'px';
		//this.imageEl.style.height = (this.imageZoom*this.containerDim.width*this.containerDim.ratio)+'px';
		
		this.imageX = this.containerDim.width*(1-this.imageZoom)/2-centerX*this.imageZoom;
		this.imageY = this.containerDim.height*(1-this.imageZoom)/2-centerY*this.imageZoom;

		this.contain(this.imageX, this.imageY, this.draggable);
		
		return true;
	},
	
	startZoomIn: function()
	{
		this.zoomBtnPressed = true;
		this.sliderAccel = .004;
		this.periodicalZoom();
		this.zoomer = new PeriodicalExecuter(this.periodicalZoom.bind(this), .05);
		return this;
	},
	
	startZoomOut: function()
	{
		this.zoomBtnPressed = true;
		this.sliderAccel = -.004;
		this.periodicalZoom();
		this.zoomer = new PeriodicalExecuter(this.periodicalZoom.bind(this), .05);
		return this;
	},
	
	stopZooming: function()
	{
		if (!this.zoomer || this.sliderSpeed==0) {
			return;
		}
		this.zoomBtnPressed = false;
		this.sliderAccel = 0;
	},
	
	periodicalZoom: function()
	{
		if (!this.zoomer) {
			return this;
		}
		
		if (this.zoomBtnPressed) {
			this.sliderSpeed += this.sliderAccel;
		} else {
			this.sliderSpeed /= 1.5;
			if (Math.abs(this.sliderSpeed)<.001) {
				this.sliderSpeed = 0;
				this.zoomer.stop();
				this.zoomer = null;
			}
		}
		this.slider.value += this.sliderSpeed;
		
		this.slider.setValue(this.slider.value);
		this.scale(this.slider.value);
		
		return this;
	},
	
	contain: function (x,y,draggable) {

		var dim = Element.getDimensions(draggable.element);
		
		var xMin = 0, xMax = this.containerDim.width-dim.width;
		var yMin = 0, yMax = this.containerDim.height-dim.height;
		
		x = x>xMin ? xMin : x;
		x = x<xMax ? xMax : x;
		y = y>yMin ? yMin : y;
		y = y<yMax ? yMax : y;
		
		this.imageX = x;
		this.imageY = y;
		
		this.imageEl.style.left = this.imageX+'px';
		this.imageEl.style.top = this.imageY+'px';
		
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