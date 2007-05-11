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
    
    update : function() {
        
    },
    
    _buildTemplate : function(formId) {
        this.tpl = new Ext.Template('<div>' +
            '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>' +
            '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">' +
            '<div id="{formElId}">' +
            '</div>' +
            '</div></div></div>' +
            '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>' +
            '</div>');
       //this.tpl.compile();         
       this.tpl.append(this.panel.getEl(), {formElId : formId});
    },
    
    _buildForm : function() {
        var i;
        this.frm = new Ext.form.Form(
            this.form.config
        );
        this._buildTemplate(this.form.config.id + '_El');        
        var key = null;
        for(i=0; i < this.form.elements.length; i++) {
          this._makeElement(this.frm, this.form.elements[i]);
        }
        this.frm.render(this.form.config.id + '_El');
    },
    
    _makeElement : function(form, element) {
        var i;
        switch(element.elementType) {
            case 'field' :
                form.add(this._makeField(element))
                return true;
            case 'fieldset' :
                form.fieldset(element.config);
                for(i=0; i < element.elements.length; i++) {
                    this._makeElement(form, element.elements[i]);
                }
                form.end;
                return true;
            case 'column' :
                form.column(element.config);
                for(i=0; i < element.elements.length; i++) {
                    this._makeElement(form, element.elements[i]);
                }
                form.end;
                return true;
            throw exception('This element type "' + element.elementType + '" is not supported');     
        }
        
    },
    
    _makeField : function(field) {
            var config = {
                fieldLabel : field.label,
                name : field.name,
                allowBlank : true,
                value : field.value
            };
            switch (field.ext_type) {
                case 'Checkbox' :
                    return new Ext.form.Checkbox(config);                
                case 'ComboBox' :
                    var RecordDef = Ext.data.Record.create([{name: 'value'},{name: 'label'}]);                    
                    var myReader = new Ext.data.JsonReader({root: 'values'}, RecordDef);                    
                    var store = new Ext.data.Store({
                       	reader : myReader,
                       	proxy : new Ext.data.MemoryProxy(field)
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
                    combo.setValue(field.value);
                    return combo;
                case 'DateField' :
                    return new Ext.form.DateField(config);                
                case 'NumberField' :
                    return new Ext.form.NumberField(config);                
                case 'Radio' :
                    return new Ext.form.Radio(config);                
                case 'TextArea' :
                    return new Ext.form.TextArea(config);                
                case 'TextField' :
                    return new Ext.form.TextField(config);
                case 'File' : 
                    config.inputType = 'file';
                    return new Ext.form.Field(config);
            }
            throw exception('This field type:"'+field.ext_type+'" not supported');
        
    }
     
})