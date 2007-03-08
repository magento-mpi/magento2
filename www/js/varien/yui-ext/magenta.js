Ext.Mage = new Object();

Ext.Mage.MenuHandler = {
    
    loadPanel : function() {
        var la = Ext.Mage.Collection['layout'];
        la.add('center', new Ext.ContentPanel(Ext.id(), {
            autoCreate: true
        }));
    }
}