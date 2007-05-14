Mage.core.PanelForm = function(region, config) {
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
    if (this.form) {
        this._buildForm();
    }
};

Ext.extend(Mage.core.PanelForm, Mage.core.Panel, {
    
    update : function(config) {
        var i;
        Ext.apply(this, config);
        if (this.frm) {
            for (i=0; i < this.frm.items.getCount(); i++) {
                this.frm.remove(this.frm.items[i]);
            }
            delete this.frm;
            this.panel.setContent('');
            this._buildForm();
        }
        
    },
    
    _buildTemplate : function(formId) {
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
        this.tpl.append(this.panel.getEl(), {formElId : formId});
    },
    
    _buildForm : function() {
        var i;
        this.frm = new Mage.JsonForm({
            method : 'POST',
            url : this.form.config.url,
            fileUpload : false
        });
        this._buildTemplate(this.form.config.id + '_El');        
        console.log(this.form);
        for(i=0; i < this.form.elements.length; i++) {
          this._makeElement(this.frm, this.form.elements[i]);
        }
        this.frm.render(this.form.config.id + '_El');
    },
    
    _makeElement : function(form, element) {
        var i;
        switch(element.config.type) {
            case 'fieldset' :
                form.fieldset(element.config);
                for(i=0; i < element.elements.length; i++) {
                    this._makeElement(form, element.elements[i]);
                }
                form.end;
                return true;
                break;
            case 'column' :
                form.column(element.config);
                for(i=0; i < element.elements.length; i++) {
                    this._makeElement(form, element.elements[i]);
                }
                form.end;
                return true;
                break;                
            default :
                form.add(this._makeField(element))
                return true;
                break;                
        }
    },
    
    _makeField : function(field) {
            var config = {
                fieldLabel : field.config.label,
                name : field.config.name,
                allowBlank : true,
                value : field.config.value
            };
            switch (field.config.ext_type) {
                case 'checkbox' :
                    return new Ext.form.Checkbox(config);                
                case 'combobox' :
                    var RecordDef = Ext.data.Record.create([{name: 'value'},{name: 'label'}]);                    
                    var myReader = new Ext.data.JsonReader({root: 'values'}, RecordDef);                    
                    var store = new Ext.data.Store({
                       	reader : myReader,
                       	proxy : new Ext.data.MemoryProxy(field.config)
                    });
                    store.load();
                    config.store = store;
                    config.displayField = 'label';
                    config.valueField = 'value';
                    config.mode = 'local';
                    config.typeAhead = true;
                    config.triggerAction = 'all';
                    config.forceSelection = true;
                    var combo = new Ext.form.ComboBox(config);
                    combo.setValue(field.config.value);
                    return combo;
                case 'datefield' :
                    return new Ext.form.DateField(config);                
                case 'numberfield' :
                    return new Ext.form.NumberField(config);                
                case 'radio' :
                    return new Ext.form.Radio(config);                
                case 'textarea' :
                    return new Ext.form.TextArea(config);                
                case 'textfield' :
                    return new Ext.form.TextField(config);
                case 'file' : 
                    config.inputType = 'file';
                    return new Ext.form.Field(config);
            }
            throw 'This field type:"'+field.ext_type+'" not supported';
        
    }
     
})