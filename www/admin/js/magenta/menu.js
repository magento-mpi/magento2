Mage.MenuHandler = {
    
        makeAction : function(node, e, data) {
            Mage.Catalog.init();
        },
    
        loadScript : function (node, data) {
            var success = function(o) { try { eval(o.responseText); } catch(e) { Ext.dump(e); } }
            var failure = function(o) { Ext.dump(o.statusText); }
            var con = new Ext.lib.Ajax.request('GET', data.url, {success:success,failure:failure});  
        },
        
        loadPanel : function(node, e, data) {
            var activePanel = null;
            var la = Mage.Collection.get('layout');

            // PanelId for new panel (node.id - id of clicked button)
    		var	panelName = node.id + '_panel';
    
            // get center region - container for panel
            var center = la.getRegion('center');

            // get panel from Mage element collection
            var panel = Mage.Collection.get(panelName);
            
            // if we have panel in Collection check if we have this panl in region
            if (panel) {
                // get activePanel if exists make active
                activePanel = center.getPanel(panel.getId());
            }
            
            if (activePanel) {
                la.showPanel(activePanel);
            } else {
                if (data.loadScript) {
                    this.loadScript(node, data);
                } else {
                    this.createPanel(node, data);
                }
            }    
        },
    
        createPanel : function(node, data) {
            var la = Mage.Collection.get('layout');
            var	panelName = node.id + '_panel';
			 if (!document.getElementById(panelName)) {
			 	// if container not found - make new
			 	divHolder = Ext.DomHelper.append(document.body, {id:panelName, tag: 'div'});
			}

			// start update layout
			la.beginUpdate();
			
			// create new panel with parameters, it is ajax panel - load contet from server
			Mage.Collection.add(panelName, new Ext.ContentPanel(panelName, {
                title : data.title,
                autoCreate: true,
                url: data.url,
                loadOnce : true,
                closable : false
			})) 
			
			la.add('center', Mage.Collection.get(panelName));
			// after creation this panel is active
			la.endUpdate();
        }
};

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
                    new Ext.menu.Item({
                        text: 'New Product',
                        handler: Mage.Catalog_Product.create.createDelegate(Mage.Catalog_Product)                        
                    }),
                    new Ext.menu.Item({
                        text: 'New Category',
                        handler: Mage.Catalog_Category.create.createDelegate(Mage.Catalog_Category)                        
                    })
                ]
            });
            Mage.Core.addLeftToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Catalog and Products',
                menu: menu
            });
            
            ////////////////////////
            // Temp - need load websites
            Mage.Core.addRightToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Website',
                menu: new Ext.menu.Menu({
                    id: 'website',
                    items: [
                        '<b>Choose a Website</b>',
                        new Ext.menu.CheckItem({
                            text: 'Aero Glass',
                            checked: true,
                            group: 'theme'
                        }),
                        new Ext.menu.CheckItem({
                            text: 'Vista Black',
                            group: 'theme'
                        }),
                        new Ext.menu.CheckItem({
                            text: 'Gray Theme',
                            group: 'theme'
                        }),
                        new Ext.menu.CheckItem({
                            text: 'Default Theme',
                            group: 'theme'
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