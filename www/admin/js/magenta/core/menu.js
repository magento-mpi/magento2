Mage.Menu_Core = function(){
    var menu = null;
    return {
        add : function(config){
            if (menu) {
                menu.add(config);
            }
        },
        
        init : function(){
            menu = new Ext.menu.Menu({
                id: 'mainSystemMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Modules'                  
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'Websites'                  
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'Apply DB Updates',
                        handler: Mage.Core.applyDbUpdates.createDelegate(Mage.Core)
                    })
                 ]
            });
            
            Mage.Core.addLeftToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'System',
                menu: menu
            });
            
            function chooseTheme(item, e) {
                Cookies.set('admtheme', item.value);
                setTimeout(function(){
                    window.location.reload();
                }, 250);                
            }

           Mage.Core.getRighToolbar().add({
                cls: 'x-btn-text-icon bmenu',
                text:'Theme',
                menu: new Ext.menu.Menu({
                    id: 'website',
                    items: [
                        '<div class="choose-theme">Choose theme</div>',
                        new Ext.menu.CheckItem({
                            text: 'Magento',
                            checked: (Cookies.get('admtheme') == 'magento') || false,
                            group: 'theme',
                            value : 'magento',
                            handler : chooseTheme
                        }),
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
            Mage.Core.getRighToolbar().add({
                cls: 'x-btn-text-icon btn-logout',
                text:'Logout'
            });
        }
    }
}();
Ext.EventManager.onDocumentReady(Mage.Menu_Core.init, Mage.Menu_Core, true);
//Mage.Menu_Core.init();
