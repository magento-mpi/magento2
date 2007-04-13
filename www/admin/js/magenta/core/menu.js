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

            ////////////////////////
            // Temp - need load websites
            var ds = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({
                    url: Mage.url + '/mage_core/search/do/'
                }),
                reader: new Ext.data.JsonReader({
                    root: 'topics',
                    totalProperty: 'totalCount',
                    id: 'post_id'
                }, [
                    {name: 'title', mapping: 'topic_title'},
                    {name: 'topicId', mapping: 'topic_id'},
                    {name: 'author', mapping: 'author'},
                    {name: 'excerpt', mapping: 'post_text'}
                ])
            });

            // Custom rendering Template
            var resultTpl = new Ext.Template(
                '<div class="search-item">',
                    '<h3><span>Date<br />by {author}</span>{title}</h3>',
                    '{excerpt}',
                '</div>'
            );            
            
            var search = Ext.DomHelper.append(document.body, {tag:'input', cls : 'search-input', type:'text', style:'visibility:hidden'}, true);
            Ext.EventManager.onDocumentReady(function(){this.toggle(true)}, search, true);
            
            var comboSearch = new Ext.form.ComboBox({
                store: ds,
                displayField:'title',
                typeAhead: false,
                loadingText: 'Searching...',
                width: 250,
                pageSize:2,
                hideTrigger:true,
                tpl: resultTpl,
                onSelect: function(record){ // override default onSelect to do redirect
                    Ext.dump(record);
                }
           });
           // apply it to the exsting input element
           comboSearch.applyTo(search);            
           
           
           Mage.Core.addRightToolbarItem(new Ext.Toolbar.Item(comboSearch.getEl().dom));
            
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
Mage.Menu_Core.init();
