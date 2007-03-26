Mage.Menu_Catalog = function(){
    var menu;
    return {
        init : function(){
            menu = new Ext.menu.Menu({
                id: 'mainCatalogMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Categories and Products',
                        handler: Mage.Catalog.loadMainPanel.createDelegate(Mage.Catalog)
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'Category attributes',
                        handler: Mage.Catalog_Category.loadAttributesPanel.createDelegate(Mage.Catalog_Category)                        
                    }),
                    new Ext.menu.Item({
                        text: 'Product attributes',  
                        handler: Mage.Product_Attributes.loadMainPanel.createDelegate(Mage.Product_Attributes)                                                
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'Product datafeeds'                  
                    })
                 ]
            });
            Mage.Core.addLeftToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Catalog',
                menu: menu
            });
        }
    }
}();
Mage.Menu_Catalog.init();
