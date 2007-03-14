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
            Mage.Core.addToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Catalog and Products',
                menu: menu
            });
        }
    }
}();
Mage.Catalog.init();
//Ext.EventManager.onDocumentReady(Mage.Catalog.init, Mage.Catalog, true);