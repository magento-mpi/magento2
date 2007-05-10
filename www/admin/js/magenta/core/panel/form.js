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
        this.buildForm();
    }
};

Ext.extend(Mage.core.PanelForm, Mage.core.Panel, {
    buildForm : function() {
        console.log(this.config);
        this.frm = new Ext.form.Form({
            fileUpload : false,
            labelAlign : 'right',
            method : 'POST',
            url : 'test'
        });
        
         this.frm.fieldset({legend:'General'});
        
        for(i=0; i < this.form.fields.length; i++) {
            var field = this.form.fields[i];
            var config = {
                fieldLabel : field.label,
                name : field.name,
                allowBlank : true,
                value : field.value
            };
            switch (field.ext_type) {
                case 'Checkbox' :
                    this.frm.add(new Ext.form.Checkbox(config));                
                break;
                case 'ComboBox' :
                //    this.frm.add(new Ext.form.ComboBox(config));                
                break;
                case 'DateField' :
                    this.frm.add(new Ext.form.DateField(config));                
                break;
                case 'NumberField' :
                    this.frm.add(new Ext.form.NumberField(config));                
                break;
                case 'Radio' :
                    this.frm.add(new Ext.form.Radio(config));                
                break;
                case 'TextArea' :
                    this.frm.add(new Ext.form.TextArea(config));                
                break;
                case 'TextField' :
                    this.frm.add(new Ext.form.TextField(config));
                break;
                case 'File' : 
                    config.inputType = 'file';
                    this.frm.add(new Ext.form.Field(config));
                break;
            }
        }
        
        this.frm.end();
        
        this.frm.render(this.panel.getEl().createChild({tag : 'div'}));
    }  
})