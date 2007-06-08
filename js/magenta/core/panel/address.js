Mage.core.PanelAddresses = function(region, config) {
    this.region = region;
    this.config = config;
    this.notLoaded = true;
    this.saveVar = null;
    this.storeUrl = Mage.url + 'customer/addressList/';
    this.tbItems = new Ext.util.MixedCollection();
    this.addressForm = null;
    
    Ext.apply(this, config);

    var layout = new Ext.BorderLayout(this.region.getEl().createChild({tag : 'div'}) ,{
        center : {
            titlebar : true,
            hideWhenEmpty : false,
            autoScroll : true            
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
        title : 'Address List',
        autoScroll : true        
    })).getEl();    
    this.addressFormEl = layout.add('center', new Ext.ContentPanel(Ext.id(),{
        autoCreate : true,
        title : 'Form',
        autoScroll : true        
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
                this.tbItems.add('addresses_delete', new Ext.Toolbar.Button({
                    text : 'Delete Address',
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

        this.view.on('selectionchange', function(view, selections){
            var selectedEl = view.getSelectedIndexes();
            var addressItem = this.view.store.getAt(selectedEl[0]);
            
            if (addressItem) {
            
                var conn = new Ext.data.Connection();
            
                conn.on('requestcomplete', function(tranId, response, options) {
                    var result =  Ext.decode(response.responseText);
                    if (result.error == 0) {
                        this._buildForm(result.form);
                    } else {
                        Ext.MessageBox.alert('Critical Error', result.errorMessage);
                    }
                }, this);
            
                conn.on('requestexception', function(){
                    Ext.MessageBox.alert('Critical Error', 'Request Exception');
                    });
            
                conn.request({
                    url : Mage.url + 'customer/addressForm/',
                    method : 'POST',
                    params : {id : addressItem.data.address_id}
                })
            }
        }.createDelegate(this));
        
        
        store.load();
        this.notLoaded = true;
    },
    
    _buildForm : function(formConfig) {
        var mask;
        if (!this.addressForm) {
            this._buildFormTemplate(formConfig.id + '_El');            
        } else {
            var el = Ext.get(formConfig.id + '_El');
            mask = new Ext.LoadMask(el);
            mask.onBeforeLoad();
            el.dom.innerHTML = '';
        }
        
        this.addressForm = new Mage.form.JsonForm( {
            method : formConfig.method,
            name : formConfig.name,
            action : formConfig.action,
            fileUpload : formConfig.fileupload,
            metaData : formConfig.formElements
        });

        this.addressForm.render(formConfig.id + '_El');
        if (mask) {
            mask.onLoad();
        }        
    },
    
    _buildFormTemplate : function(formId) {
        if (!this.tpl) {
            this.tpl = new Ext.Template('<div>' +
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>' +
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">' +
                '<div id="{formElId}">' +
                '</div>' +
                '</div></div></div>' +
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>' +
                '</div>');
           this.tpl.compile();         
        }
        this.tpl.append(this.addressFormEl, {formElId : formId});
    }
})
