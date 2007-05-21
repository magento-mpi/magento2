Mage.form.FileField = function(config) {
    Ext.apply(this, config);
    Mage.form.FileField.superclass.constructor.call(this, config);
}

Ext.extend(Mage.form.FileField, Ext.form.Field, {
    inputType: 'file',
    
     onRender : function(ct, position){
         
         Mage.form.FileField.superclass.onRender.call(this, ct, position);
     }
})