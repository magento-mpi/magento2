Mage.Menu = function(){
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
            
            function chooseTheme(item, e) {
                Cookies.set('admtheme', item.value);
                setTimeout(function(){
                    window.location.reload();
                }, 250);                
            }
            
            ////////////////////////
            // Temp - need load websites
            Mage.Core.addRightToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Theme',
                menu: new Ext.menu.Menu({
                    id: 'website',
                    items: [
                        '<b>Choose the site theme</b>',
                        new Ext.menu.CheckItem({
                            text: 'Aero Glass',
                            checked: (Cookies.get('admtheme') == 'aero') || false,
                            group: 'theme',
                            value : 'aero',
                            handler : chooseTheme
                        }),
                        new Ext.menu.CheckItem({
                            text: 'Vista Black',
                            checked: (Cookies.get('admtheme') == 'vista') || false,
                            group: 'theme',
                            value : 'vista',
                            handler : chooseTheme                            
                        }),
                        new Ext.menu.CheckItem({
                            text: 'Gray Theme',
                            group: 'theme',
                            checked: (Cookies.get('admtheme') == 'gray') || false,
                            value : 'gray',
                            handler : chooseTheme                            
                        }),
                        new Ext.menu.CheckItem({
                            text: 'Default Theme',
                            group: 'theme',
                            checked: (Cookies.get('admtheme') == 'default') || false,
                            value : 'default',
                            handler : chooseTheme                            
                        })
                    ]
                })
            });
            Mage.Core.addRightToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Logout'
            });
        }
    }
}();
Mage.Menu.init();
//Ext.EventManager.onDocumentReady(Mage.Catalog.init, Mage.Catalog, true);