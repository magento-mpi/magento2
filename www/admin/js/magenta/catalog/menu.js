Mage.Menu_Catalog = function(){
    var menu;
    return {
        init : function(){
            menu = new Ext.menu.Menu({
                id: 'mainMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Categories and Products',
                        handler: Mage.Catalog.loadMainPanel.createDelegate(Mage.Catalog)
                    }),
                    '-',
/*                    new Ext.menu.Item({
                        text: 'New Product',
                        handler: Mage.Catalog_Product.create.createDelegate(Mage.Catalog_Product)                        
                    }),*/
                    new Ext.menu.Item({
                        text: 'New Category',
                        handler: Mage.Catalog_Category.create.createDelegate(Mage.Catalog_Category)                        
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'Category attributes',
                        handler: Mage.Catalog_Category.loadAttributesPanel.createDelegate(Mage.Catalog_Category)                        
                    })
                ]
            });
            Mage.Core.addLeftToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Catalog and Products',
                menu: menu
            });
        }
    }
}();
Mage.Menu_Catalog.init();
