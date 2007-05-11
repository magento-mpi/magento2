Mage.core.PanelImages = function(region, config) {
    this.region = region;
    Ext.apply(this, config);
    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
       	autoScroll : true,
       	fitToFrame : true,       	
        title : this.title || 'Images'
    }));
    
    this._build();
};

Ext.extend(Mage.core.PanelImages, Mage.core.Panel, {
    update : function(config) {
        
    },
    
    
    _build : function() {
        this.containerEl = this._buildTemplate();
        
        var formContainer = this.containerEl.createChild({tag : 'div'});        
        var viewContainer = this.containerEl.createChild({tag : 'div'});
        
        this._buildForm(formContainer);
        this._buildImagesView(viewContainer);  
    },
    
    _buildForm : function(formContainer) {
        var form = new Ext.form.Form(); 
        var config = {
          fieldLabel : 'Image',
          name : 'image',
          allowBlank : true,
          inputType : 'file'
        };
        
        var file = new Ext.form.Field(config)
        file.on('change', function(field, value, orginValue){
           if (value != "") {
               form.submit({url:this.saveUrl, waitMsg:'Saving Data...'});               
           }
        }, this);
        form.add(file);
        form.render(formContainer);       
    },
    
    _buildImagesView : function(viewContainer) {
        
        this.dataRecord = Ext.data.Record.create([
            {name: 'id'},
            {name: 'src'},
            {name: 'alt'},
            {name: 'description'}
        ]);

        var dataReader = new Ext.data.JsonReader({
            root: 'items',
            totalProperty: 'totalRecords',
            id: 'id'
        }, this.dataRecord);
    
    
        var store = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url: this.url}),
            reader: dataReader,
        });
        
        
        var viewTpl = new Ext.Template('<div id="{id}"><img src="{src}" alt="{alt}"></div>');
        var view = new Ext.View(viewContainer, viewTpl,{
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