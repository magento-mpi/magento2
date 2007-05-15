Mage.form.FileField = function(config) {
    Ext.apply(this, config);
    Mage.form.FileField.superclass.constructor.call(this, config);
}

Ext.extend(Mage.form.FileField, Ext.form.Field, {
    inputType: 'file',
    
     onRender : function(ct, position){
        Mage.form.FileField.superclass.onRender.call(this, ct, position);
        console.log(this);
        if (this.autoSubmit == true && this.form) {
            this.on('change', function() {
                this.form.submit({waitMsg:'Upload File...'});    
            }.createDelegate(this));
        }
     }
})