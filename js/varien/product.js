function openLargeImageWin(url, width, height)
{
	win = window.open(url,'largimage','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+width+',height='+height+',screenX=150,screenY=150,top=150,left=150');
	win.focus();
}

if(typeof Product=='undefined') {
	var Product = {};
}

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