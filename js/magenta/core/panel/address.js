Mage.core.PanelAddresses = function(region, config) {
    this.region = region;
    this.config = config;
    this.notLoaded = true;
    this.saveVar = null;
    this.tbItems = new Ext.util.MixedCollection();

    Ext.apply(this, config);
    
    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
        background : config.background || true,
       	autoScroll : true,
       	fitToFrame : true,
        title : this.title || 'Title'
    }));
 
    this.panel.on('activate', function(){
        this._build();
    }, this, {single: true});	    	

    this.panel.on('activate', function(){
        this._loadActions();
        if (this.notLoaded) {
            this.notLoaded = false;
        }
    }, this);	    	

    this.panel.on('deactivate', this._unLoadActions, this);

};

Ext.extend(Mage.core.PanelAddresses, Mage.core.Panel, {
    
    update : function(config) {
        Ext.apply(this, config);
        if (this.region.getActivePanel() === this.panel) {
            this.notLoaded = false;            
        } else {
            this.notLoaded = true;            
        }
    },
    
    save : function() {
    },
    
    _loadActions : function() {
        if (this.toolbar) {
            if (this.tbItems.getCount() == 0) {
                this.tbItems.add('addresses_sep', new Ext.Toolbar.Separator());
                this.tbItems.add('addresses_remove', new Ext.Toolbar.Button({
                    text : 'Remove Address',
                    handler : this._onDeleteItem,
                    scope : this
                }));
                
                this.tbItems.each(function(item){
                    this.toolbar.add(item);
                }.createDelegate(this));
            } else {
                this.tbItems.each(function(item){
                    item.show();
                }.createDelegate(this));
            }
        }
    },
    
    _unLoadActions : function() {
        this.tbItems.each(function(item){
            item.hide();
        }.createDelegate(this));
    },    
    
    _build : function() {
    }
})
