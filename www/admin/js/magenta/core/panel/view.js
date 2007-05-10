Mage.core.PanelView = function(region, config) {
    this.region = region;
    Ext.apply(this, config);
    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
        url : this.url || null,
        loadOnce : true,
       	autoScroll : true,
       	fitToFrame : true,       	
        title : this.title || 'Title'
    }));
};

Ext.extend(Mage.core.PanelView, Mage.core.Panel, {
    update : function(config) {
        Ext.apply(this, config);
        if (this.region.getActivePanel() == this.panel) {
            this.panel.load(this.url);
        } else {
            this.panel.setUrl(this.url,{}, true);
        }
    }
})