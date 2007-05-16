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
    this._build();
};

Ext.extend(Mage.core.PanelCategories, Mage.core.Panel, {
    
    update : function(config) {
    },
     
    _build : function() {
        
    },
    
    _buildView : function(viewContainer) {
        
        this.dataRecord = Ext.data.Record.create([
            {name: 'id'},
            {name: 'src'},
            {name: 'alt'},
            {name: 'description'}
        ]);

        var dataReader = new Ext.data.JsonReader({
            root: 'items',
            totalProperty: 'totalRecords'
        }, this.dataRecord);
    
    
        var store = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url: this.storeUrl}),
            reader: dataReader
        });
        
        
        var viewTpl = new Ext.Template('<div class="thumb-wrap" id="{name}">' +
                '<div id="{id}" class="thumb"><img src="{src}" alt="{alt}"></div>' +
                '<span>some text</span>' +
                '</div>');
                
        this.imagesView = new Ext.View(viewContainer, viewTpl,{
            singleSelect: true,
            store: store,
            emptyText : 'Images not found'
        });
        
        store.load();
    },
})