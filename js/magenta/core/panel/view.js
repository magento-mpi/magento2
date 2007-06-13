Mage.core.PanelView = function(region, config) {
    this.region = region;
    this.notLoaded = true;    

    Ext.apply(this, config);
    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
        url : this.url || null,
        loadOnce : true,
       	autoScroll : true,
       	fitToFrame : true,       	
        title : this.title || 'Title'
    }, this.content));
    
    this.panel.getUpdateManager().on('update', function() {
        this.notLoaded = false;
    }, this)
    

    this.panel.on('activate', function(){
        if (this.notLoaded) {
            this.panel.setContent(this.content);
            if (typeof this.url == 'string' && this.url != '') {
                this.panel.load(this.url);
            }
        }
    }, this);
};

Ext.extend(Mage.core.PanelView, Mage.core.Panel, {
    update : function(config) {
        Ext.apply(this, config);
        if (this.region.getActivePanel() == this.panel) {
            this.panel.setContent(this.content);
            if (typeof this.url == 'string' && this.url != '') {
                this.panel.load(this.url);
            }
        } else {
            this.notLoaded = true;
        }
    }
})
