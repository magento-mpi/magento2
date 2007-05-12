Mage.core.PanelCategories = function(region, config) {
    this.region = region;
    this.config = config;
    Ext.apply(this, config);
    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
        background : config.background || true,
       	autoScroll : true,
       	fitToFrame : true,
        title : this.title || 'Title'
    }));
};

Ext.extend(Mage.core.PanelCategories, Mage.core.Panel, {
    
    update : function(config) {
    }
     
})