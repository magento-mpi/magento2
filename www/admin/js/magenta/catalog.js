Mage.Catalog = function(){
    var _layout = false;
    var _menu;
    return {
        init : function(){
            _menu = new Ext.menu.Menu({
                id: 'mainMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Categories and Products',
                        handler : Mage.Catalog.loadPanel,
                        scope: this
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'New Product',
                    }),
                    new Ext.menu.Item({
                        text: 'New Category',
                    }),
                    '-',
                    new Ext.menu.Item({
                        text: 'Attributes',
                    })
                ]
            });
            Mage.Core.addToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Catalog and Products',
                menu: _menu
            });
        },
        addMenuItem : function(){
            
        },
        loadPanel : function(){
            if (!this._layout){
                _layout = new Ext.BorderLayout( Ext.DomHelper.append(Mage.Core.getLayout().getEl(), {tag:'div'}, true), {
                        "west":{
                            "split":true,
                            "autoScroll":true,
                            "collapsible":true,
                            "titlebar":true
                            },
                        "center":{
                            "autoScroll":false,
                            "titlebar":false,
                            "hideTabs":true
                            }
                    );

Mage.Collection.add('catalog_main_left_border', new Ext.BorderLayout(Ext.DomHelper.append(Mage.Core.getLayout().getEl(), {tag:'div'}, true), {"center":{"autoScroll":true,"titlebar":false},"south":{"split":true,"initialSize":200,"minSize":50,"maxSize":400,"autoScroll":true,"collapsible":true}}));
Mage.Collection.add('catalog_main_left_tree_panel', new Ext.ContentPanel('catalog_main_left_tree_panel', {"url":"\/admin\/mage_catalog\/tree","autoCreate":true}));
Mage.Collection.add('catalog_main_left_preview_panel', new Ext.ContentPanel('catalog_main_left_preview_panel', {"autoCreate":true}));
Mage.Collection.get('catalog_main_left_border').beginUpdate();
Mage.Collection.get('catalog_main_left_border').add('center', Mage.Collection.get('catalog_main_left_tree_panel'));
Mage.Collection.get('catalog_main_left_border').add('south', Mage.Collection.get('catalog_main_left_preview_panel'));
Mage.Collection.get('catalog_main_left_border').endUpdate();
Mage.Collection.add('catalog_main_left_panel', new Ext.NestedLayoutPanel(Mage.Collection.get('catalog_main_left_border'), []));
Mage.Collection.add('catalog_main_content_panel', new Ext.ContentPanel('catalog_main_content_panel', {"url":"\/admin\/mage_catalog\/","autoCreate":true}));
Mage.Collection.add('catalog_main_content_border_panel', new Ext.BorderLayout(Ext.DomHelper.append(Mage.Core.getLayout().getEl(), {tag:'div'}, true), {"north":{"split":true,"initialSize":200,"minSize":50,"maxSize":400,"autoScroll":true,"collapsible":true},"center":{"autoScroll":true,"titlebar":false},"south":{"split":true,"initialSize":200,"minSize":50,"maxSize":400,"autoScroll":true,"collapsible":true}}));
Mage.Collection.add('catalog_main_content_north_panel', new Ext.ContentPanel('catalog_main_content_north_panel', {"autoCreate":true}));
Mage.Collection.add('catalog_main_content_center_panel', new Ext.ContentPanel('catalog_main_content_center_panel', {"title":"Dashboard","url":"\/admin\/mage_catalog\/","loadOnce":true,"autoCreate":true}));
Mage.Collection.add('catalog_main_content_south_panel', new Ext.ContentPanel('catalog_main_content_south_panel', {"autoCreate":true}));
Mage.Collection.get('catalog_main_content_border_panel').beginUpdate();
Mage.Collection.get('catalog_main_content_border_panel').add('north', Mage.Collection.get('catalog_main_content_north_panel'));
Mage.Collection.get('catalog_main_content_border_panel').add('center', Mage.Collection.get('catalog_main_content_center_panel'));
Mage.Collection.get('catalog_main_content_border_panel').add('south', Mage.Collection.get('catalog_main_content_south_panel'));
Mage.Collection.get('catalog_main_content_border_panel').endUpdate();
Mage.Collection.add('catalog_main_content_panel1', new Ext.NestedLayoutPanel(Mage.Collection.get('catalog_main_content_border_panel'), []));
Mage.Collection.get('catalog_main_panel_border').beginUpdate();
Mage.Collection.get('catalog_main_panel_border').add('west', Mage.Collection.get('catalog_main_left_panel'));
Mage.Collection.get('catalog_main_panel_border').add('center', Mage.Collection.get('catalog_main_content_panel'));
Mage.Collection.get('catalog_main_panel_border').add('center', Mage.Collection.get('catalog_main_content_panel1'));
Mage.Collection.get('catalog_main_panel_border').endUpdate();
Mage.Collection.add('catalog_main_panel', new Ext.NestedLayoutPanel(Mage.Collection.get('catalog_main_panel_border'), {"title":"Products and Categories","closable":true}));
            }
        }
    }
}();
Mage.Catalog.init();
//Ext.EventManager.onDocumentReady(Mage.Catalog.init, Mage.Catalog, true);