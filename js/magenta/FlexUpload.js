Mage.FlexUpload = function () {
	Mage.FlexUpload.superclass.constructor.apply(this, arguments);
	this.loaded = false;
	this.events = {
		load : true,
		preinitialize: true,
		initialize: true,
		select: true,
		beforeupload: true,
		progress: true,
		afterupload: true
	};
	
	this.setAttributes( 
		{"src" : Mage.url + "../media/flex/upload.swf"}
	);
	
	this.addListener( 'load', function(eventData) { this.loaded = true } );
};

Ext.extend( Mage.FlexUpload, Mage.FlexObject, {
	
	setConfig : function() {
		
		if(this.loaded && arguments.length > 1) 
		{
			this.getApi().setConfig( arguments[0], arguments[1] );
		}
		else if(this.loaded && arguments.length == 1)
		{
			this.getApi().setConfig( arguments[0] );
		}
		
	}
} );