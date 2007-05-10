Mage.core.PanelForm = function(region, config) {
    this.region = region;
    this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
        background : true,
        url : config.url || null,
        loadOnce : true,
       	autoScroll : true,
       	fitToFrame : true,
        title : config.title || 'Title'
    }));
};

Ext.extend(Mage.core.PanelForm, Mage.core.Panel, {
    
})