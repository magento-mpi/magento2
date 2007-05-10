Mage.Menu_Core = function(){
    var menu = null;
    return {
        add : function(config){
            if (menu) {
                menu.add(config);
            }
        },
        
        init : function(){
            var aboutmenu = new Ext.menu.Menu({                
                id: 'aboutMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'About',
                        handler : function(){
                            Ext.MessageBox.alert('About','<img src="skins/default/images/logo2.gif" /><br><center>Version <b>Alpha</b></center>');
                        }                  
                    }),
                    new Ext.menu.Item({
                        text: 'Credits',
                        handler : function(){
                            Ext.MessageBox.alert('Credits','Andrey&nbsp;Korolyov&nbsp;<a href="mailto:andrey@varien.com">andrey@varien.com</a><br> Dmitriy&nbsp;Soroka&nbsp;<a href="mailto:dmitry@varien.com">dmitry@varien.com</a><br> Moshe&nbsp;Gurvich&nbsp;<a href="mailto:moshe@varien.com">moshe@varien.com</a><br><img src="extjs/resources/images/default/s.gif" width="250" height="1"/>');
                        }
                    }),
                 ]
            });
            
            Mage.Admin.addLeftToolbarItem({
                icon: 'favicon.ico',
                cls: 'x-btn-icon',                
                menu: aboutmenu
            });
            
            
            var menu = new Ext.menu.Menu({
                id: 'mainSystemMenu',
                items: [
/*
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
                        handler: Mage.Admin.applyDbUpdates.createDelegate(Mage.Admin)
                    })
*/
                 ]
            });
            
            Mage.Admin.addLeftToolbarItem({
                cls: 'x-btn-text bmenu',
                text:'System',
                menu: menu
            });
        },
        
        initRight: function(){
            function chooseTheme(item, e) {
                var themeStyleEl =  Ext.get('theme_stylesheet');
                Cookies.set('admtheme', item.value);
                themeStyleEl.dom.href = themeStyleEl.dom.href.replace(/(ytheme-).*(\.css)$/, '$1'+item.value+'$2');
            }

           Mage.Admin.getRighToolbar().add({
                cls: 'x-btn-text .btn-theme',
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
            Mage.Admin.getRighToolbar().add({
                cls: 'x-btn-text-icon btn-logout',
                text:'Logout',
                handler: function(){
                    window.location = Mage.url + 'index/logout/'
                }
            });
        }
    }
}();
Ext.EventManager.onDocumentReady(Mage.Menu_Core.initRight, Mage.Menu_Core, true);
Mage.Menu_Core.init();
