Mage.core.PanelAddresses = function(region, config) {
    this.region = region;
    this.config = config;
    this.notLoaded = true;
    this.saveVar = null;
    this.storeUrl = Mage.url + 'customer/addressList/';
    this.tbItems = new Ext.util.MixedCollection();

    Ext.apply(this, config);

    var layout = new Ext.BorderLayout(this.region.getEl().createChild({tag : 'div'}) ,{
        center : {
            titlebar : true,
            hideWhenEmpty : false
        },        west : {
            split:true,
            initialSize: 200,
            minSize: 175,
            maxSize: 400,
            titlebar: true,
            collapsible: true,
            hideWhenEmpty : false            
        }
    });
    layout.beginUpdate();    
    
    this.addressBaseEl = layout.add('west', new Ext.ContentPanel(Ext.id(),{
        autoCreate : true,
        title : 'Address List'
    })).getEl();    
    this.addressFormEl = layout.add('center', new Ext.ContentPanel(Ext.id(),{
        autoCreate : true,
        title : 'Form'
    })).getEl();    
    
    
    layout.endUpdate();
    
    this.panel = this.region.add(new Ext.NestedLayoutPanel(layout, {
        background : config.background || true,
        title : this.title || 'Title'
    }));
 
    this.panel.on('activate', function(){
        this._build();
    }, this, {single: true});           

    this.panel.on('activate', function(){
        this._loadActions();
        if (this.notLoaded) {
            this.view.store.proxy.getConnection().url = this.storeUrl;
            this.view.store.load();
            this.notLoaded = false;
        }
    }, this);           

    this.panel.on('deactivate', this._unLoadActions, this);

};

Ext.extend(Mage.core.PanelAddresses, Mage.core.Panel, {
    
    update : function(config) {
        Ext.apply(this, config);
        if (this.region.getActivePanel() === this.panel) {
            this.view.store.proxy.getConnection().url = this.storeUrl;
            this.view.store.load();
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
        this._buildAddressView();
    },
    
    _buildAddressView : function() {
        
        this.dataRecord = Ext.data.Record.create([
            {name: 'address_id'},
            {name: 'address'}
        ]);

        var dataReader = new Ext.data.JsonReader({
            root: 'addresses',
            id : 'address_id'
        }, this.dataRecord);
    

        
        var store = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url:this.storeUrl}),
            reader: dataReader
        });
        
        store.on('load', function() {
            if (this.view) {
                this.view.select(0);
            }
        }.createDelegate(this));

        this.LoadMask = new Ext.LoadMask(this.panel.getEl(), {
            store: store
        });
        
        var viewTpl = new Ext.Template(
            '<div id="{address_id}" class="address-view">' +
                '<address>{address}</address>' +
            '</div>'
        );
        viewTpl.compile();
                   
        this.view = new Ext.View(this.addressBaseEl.createChild({tag : 'div'}), viewTpl,{
            singleSelect: true,
            store: store,
            emptyText : 'Addresses not found'
        });
        
        this.view.on('beforeselect', function(view){
            return view.store.getCount() > 0;
        });
        
        this.view.on('selectionchange', function(view, selections){
            if (this.tbItems.get('addresses_delete')) {
                if (selections.length) {
                    this.tbItems.get('addresses_delete').enable();
                } else {
                    this.tbItems.get('addresses_delete').disable();
                }
            }
        }.createDelegate(this));
        
        store.load();
        this.notLoaded = true;
    }
})
