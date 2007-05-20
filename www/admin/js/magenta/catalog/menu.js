Mage.Menu_Catalog = function(){
    var menu;
    return {
        init : function(toolbar){
            menu = new Ext.menu.Menu({
                id: 'mainCatalogMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Categories and Products',
                        handler : Mage.Admin.callModuleMethod.createDelegate(Mage.Admin, ['catalog', 'loadMainPanel'], 0)                        
                    }),
                    '-',
/*
                    new Ext.menu.Item({
                        text: 'Category attributes',
                        handler: Mage.Catalog_Category_Attributes.loadAttributesPanel.createDelegate(Mage.Catalog_Category_Attributes)                        
                    }),
*/
                    new Ext.menu.Item({
                        text: 'Product attributes',  
                        handler : Mage.Admin.callModuleMethod.createDelegate(Mage.Admin, ['product_attirbutes', 'loadMainPanel'], 0)                                                
                    })
/*
                    '-',
                    new Ext.menu.Item({
                        text: 'Product datafeeds'                  
                    })
*/
                 ]
            });
            
           toolbar.addButton({
                cls: 'x-btn-text bmenu',
                text:'Catalog',
                menu: menu
            });
        }
    }
}();