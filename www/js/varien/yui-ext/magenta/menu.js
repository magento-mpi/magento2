Ext.Mage.Menu = {
    menuCollection : new Ext.util.MixedCollection(true),
    
    loadCollection : function() {
        var menu = new Ext.menu.Menu({
            id: 'catalog',
            items: [{
                text: 'View Products',
            }, '-' ,{
                text: 'New Category',
            },{
                text: 'New Product',
            },{
                text: 'New Attribute',
            }]
        });
        this.menuCollection.add('catalog', menu);
        menu = new Ext.menu.Menu({
            id: 'customers',
            items: [{
                text: 'View Customers & Orders',
            }, '-' ,{
                text: 'Send mass email',
            }]
        });
        this.menuCollection.add('customers', menu);
        
        menu = new Ext.menu.Menu({
            id: 'system',
            items: [{
                text: 'Manage Blocks and Layouts',
            }]
        });
        this.menuCollection.add('system', menu);
    },
    
    getMenu: function(name) {
        return this.menuCollection.get(name);
    }
}

Ext.EventManager.onDocumentReady(Ext.Mage.Menu.loadCollection, Ext.Mage.Menu, true);	