if (typeof Mage == 'undefined') {
	alert('JS Error: Core module JS not loaded');
}

Mage.Catalog = function(){
    var menu;
    return {
        init : function(){
            menu = new Ext.menu.Menu({
                id: 'mainMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Categories and Products',
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'New Product',
                    }),
                    new Ext.menu.Item({
                        text: 'New Category',
                    })
                ]
            });
            Mage.Core.getToolbar().add({
                cls: 'x-btn-text-icon bmenu', // icon and text class
                text:'Catalog and Products',
                menu: menu
            });
        }
    }
}();
Ext.EventManager.onDocumentReady(Mage.Catalog.init, Mage.Catalog, true);