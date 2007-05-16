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
    this.storeUrl = Mage.url + 'mage_catalog/product/imageCollection/product/2990/';
    this._build();
};

Ext.extend(Mage.core.PanelCategories, Mage.core.Panel, {
    
    update : function(config) {
    },
     
    _build : function() {
        this.containerEl = this._buildTemplate();
        var viewContainer = this.containerEl.createChild({tag : 'div', cls:'x-productimages-view'});
        
        this._buildView(viewContainer);  
            
    },
    
    _buildView : function(viewContainer) {
        
        this.dataRecord = Ext.data.Record.create([
            {name: 'id'},
            {name: 'path'},
            {name: 'image_src'},
            {name: 'image_alt'}
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
                '<div id="{id}" class="thumb"><img src="{image_src}" alt="{image_alt}"></div>' +
                '<span>some text</span>' +
                '</div>');
                
        this.view = new Ext.View(viewContainer, viewTpl,{
            singleSelect: true,
            store: store,
            emptyText : 'Categories not set'
        });
        
        dd = new Ext.dd.DragDrop(this.view.getEl(), "TreeDD");

        this.dropzone = new Ext.dd.DropTarget(this.view.getEl(), {
            overClass : 'm-view-overdrop'
        });
        this.dropzone.notifyDrop = function(dd, e, data){
            this.view.store.add(new this.dataRecord({
                id : data.node.id,
                name : data.node.text
            }))
            return true;
        }.createDelegate(this);
        
        store.load();
    },

    _buildTemplate : function() {
        this.tpl = new Ext.Template('<div>' +
            '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>' +
            '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">' +
            '<div id="{containerElId}">' +
            '</div>' +
            '</div></div></div>' +
            '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>' +
            '</div>');
       containerElId = Ext.id();
       var tmp = this.tpl.append(this.panel.getEl(), {containerElId : containerElId}, true);
       return Ext.get(containerElId);
    }
    
})