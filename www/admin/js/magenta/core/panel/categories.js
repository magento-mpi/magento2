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

    this.panel.on('activate', this._loadActions, this);
    this.panel.on('deactivate', this._unLoadActions, this);

    this._build();
};

Ext.extend(Mage.core.PanelCategories, Mage.core.Panel, {
    
    update : function(config) {
        Ext.apply(this, config);
        if (this.region.getActivePanel() === this.panel) {
            this.view.store.proxy.getConnection().url = this.storeUrl;
            this.view.store.load();
        }
    },
    
    _loadActions : function() {
        if (this.toolbar) {
            if (this.tbItems.getCount() == 0) {
                this.tbItems.add('categories_sep', new Ext.Toolbar.Separator());
                this.tbItems.add('categories_delete', new Ext.Toolbar.Button({
                    text : 'Delete Category'
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
        this.containerEl = this._buildTemplate();
        var viewContainer = this.containerEl.createChild({tag : 'div', cls:'x-productimages-view'});
        
        this._buildView(viewContainer);  
            
    },
    
    _buildView : function(viewContainer) {
        
        this.dataRecord = Ext.data.Record.create([
            {name: 'category_id'},
            {name: 'path'},
            {name: 'image_src'},
            {name: 'image_alt'},
            {name: 'name'}
        ]);

        var dataReader = new Ext.data.JsonReader({
            root: 'items',
            totalProperty: 'totalRecords',
            id : 'category_id'
        }, this.dataRecord);
    
    
        var store = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url: this.storeUrl}),
            reader: dataReader
        });
        
        
        var viewTpl = new Ext.Template('<div class="thumb-wrap">' +
                '<div id="{id}" class="thumb"><img src="{image_src}" alt="{image_alt}"></div>' +
                '<span>{name}</span>' +
                '</div>');
                
        this.view = new Ext.View(viewContainer, viewTpl,{
            singleSelect: true,
            store: store,
            emptyText : 'Categories not set'
        });
        
        var dd = new Ext.dd.DragDrop(this.view.getEl(), "TreeDD");
        

        

        this.dropzone = new Ext.dd.DropTarget(this.view.getEl(), {
            overClass : 'm-view-overdrop'
        });
        
        this.dropzone.notifyOver = function(dd, e, data){
            if (this.view.store.getById(data.node.id)) {
                return this.dropzone.dropNotAllowed;
            } else {
                return this.dropzone.dropAllowed;
            }
        }.createDelegate(this);
        
        this.dropzone.notifyDrop = function(dd, e, data){
            if (this.view.store.getById(data.node.id)) {
                return false;
            };
            
            var text = '';
            data.node.bubble(function(){
                if (this.isRoot || this.attributes.isRoot) {
                    return true;
                }
                console.log(this)
                if (text != '') {
                    text = this.text + '<br>' + text;    
                } else {
                    text = this.text    
                }
            });
            
            this.view.store.add(new this.dataRecord({
                category_id : data.node.id,
                name : text
            }, data.node.id));
            console.log(this.view.store);
            
            if(this.dropzone.overClass){
                this.dropzone.el.removeClass(this.dropzone.overClass);
            }            
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