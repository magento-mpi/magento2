Mage.core.PanelForm = function(region, config) {
    this.region = region;
    this.config = config;
    Ext.apply(this, config);
    this.panel = this.region.add(new Ext.ContentPanel(Ext.id(), {
        autoCreate : true,
        background : true,
        url : this.url || null,
        loadOnce : true,
       	autoScroll : true,
       	fitToFrame : true,
        title : this.title || 'Title'
    }));
    if (this.form) {
        this._buildForm();
    }
};

Ext.extend(Mage.core.PanelForm, Mage.core.Panel, {
    _buildForm : function() {
        var i;
        console.log(this.config);
        this.frm = new Ext.form.Form(
            this.form.config
        );
        var key = null;
        for(i=0; i < this.form.elements.length; i++) {
          this._makeElement(this.frm, this.form.elements[i]);
        }
        
        this.frm.render(this.panel.getEl().createChild({tag : 'div'}));
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
                    return new Ext.form.TextField(config);                
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