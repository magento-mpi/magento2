Ext.Mage = new Object();

Ext.Mage.MenuHandler = {

    loadPanel : function(node, e) {
        var la = Ext.Mage.Collection['layout'];
        la.add('center', new Ext.ContentPanel(Ext.id(), {
            title : node.text,
            autoCreate: true,
            url: this.url,
            loadOnce : true,
            closable : false
        }));
    }
}