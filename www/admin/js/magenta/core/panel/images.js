Mage.core.PanelImages = function(region, config) {
    this.region = region;
    Ext.apply(this, config);
    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
       	autoScroll : true,
       	fitToFrame : true,   
       	background : config.background || true,    	
        title : this.title || 'Images'
    }));
    console.log(config);
    this._build();
};

Ext.extend(Mage.core.PanelImages, Mage.core.Panel, {
    update : function(config) {
        if (this.region.getActivePanel() === this.panel) {
            this.imagesView.store.proxy.getConnection().url = this.storeUrl;
            this.imagesView.store.load();
        }
    },
    
    
    _build : function() {
        this.containerEl = this._buildTemplate();
        
        var formContainer = this.containerEl.createChild({tag : 'div'});        
        var viewContainer = this.containerEl.createChild({tag : 'div'});
        
        this._buildForm(formContainer);
        this._buildImagesView(viewContainer);  
    },
    
    _buildForm : function(formContainer) {
        this.frm = new Mage.form.JsonForm({
            fileUpload : this.form.config.fileupload,
            method : this.form.config.method,
            action : this.form.config.action,
            metaData : this.form.elements,
            waitMsgTarget : formContainer
        }); 
        
        this.frm.render(formContainer);       
        
        this.frm.on({
            actionfailed : function(form, action) {
                Ext.MessageBox.alert('Error', 'Error');
            },
            actioncomplete : function(form, action) {
                this.imagesView.store.add(new this.dataRecord(action.result.data));
                this.imagesView.refresh();
                form.reset();
                console.log(action.result.data);
            }.createDelegate(this)
        });
     },
    
    _buildImagesView : function(viewContainer) {
        
        this.dataRecord = Ext.data.Record.create([
            {name: 'id'},
            {name: 'file'},
            {name: 'alt'},
            {name: 'description'}
        ]);

        var dataReader = new Ext.data.JsonReader({
            root: 'items',
            totalProperty: 'totalRecords',
            id: 'id'
        }, this.dataRecord);
    
    
        var store = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url: this.storeUrl}),
            reader: dataReader
        });
        
        
        var viewTpl = new Ext.Template('<div id="{id}"><img src="{file}" alt="{alt}" width="50" border="1"></div>');
        this.imagesView = new Ext.View(viewContainer, viewTpl,{
            singleSelect: true,
            selectedClass: 'ydataview-selected',
            store: store,
            emptyText : 'Images not found'
        });
        
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
       this.tpl.append(this.panel.getEl(), {containerElId : containerElId}, true);
       return Ext.get(containerElId);
    }
})