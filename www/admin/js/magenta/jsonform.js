Mage.JsonForm = function(config) {
    Mage.JsonForm.superclass.constructor.call(this, config);
}

Ext.extend(Mage.JsonForm, Ext.form.Form, {
    render : function(ct) {
        if (this.metaData) {
            console.log(metaData);
        }
        Mage.JsonForm.superclass.render.call(this, ct);
    }
})

