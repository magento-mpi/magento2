Mage.Catalog = function(depend){
    var loaded = false;
    var Layout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        init : function() {
            
            var Core_Layout = Mage.Core.getLayout();
            if (!Layout) {
                Layout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    west: {
                        split:true,
                        autoScroll:true,
                        collapsible:true,
                        titlebar:true
                    },
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs:true
                    }
                });
                
                this._layouts.add('main', Layout);
                
                var Layout_West = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                        center: {
                            collapsedTitle : '<b>Categories Tree</b>',
                            autoScroll:true,
                            titlebar:false
                        }, 
                        south: {
                            split:true,
                            initialSize:200,
                            minSize:50,
                            maxSize:400,
                            autoScroll:true,
                            collapsible:true
                         }
                     }
                );
                
                this._layouts.add('tree', Layout_West);
                
                Layout_West.beginUpdate();
                Layout_West.add('center', new Ext.ContentPanel('catalog_main_left_tree_panel', {url: Mage.url + '/mage_catalog/category/tree', autoCreate:true}));
                Layout_West.add('south', new Ext.ContentPanel('catalog_main_left_preview_panel', {autoCreate:true}));
                Layout_West.endUpdate();
                
                
                var Layout_Center = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    north: {
                        titlebar:true,
                        split:true,
                        initialSize:83,
                        minSize:0,
                        maxSize:200,
                        autoScroll:true,
                        collapsible:true
                     },
                     center:{
                         titlebar: true,
                         autoScroll:true,
                         resizeTabs : true,
                         hideTabs : true,
                         tabPosition: 'top'
                     },
                     south: {
                         split:true,
                         initialSize:200,
                         minSize:50,
                         maxSize:400,
                         titlebar: true,
                         autoScroll:true,
                         collapsible:true,
                         hideTabs : true
                      }
                 });
                
                this._layouts.add('workZone', Layout_Center);

//                var NestedLayout_Center = new Ext.BorderLayout();
                Layout_Center.beginUpdate();
                //Layout_Center.add('north', new Ext.ContentPanel('catalog_layout_center_north_panel', {autoCreate:true}));
                Layout_Center.add('center', new Ext.ContentPanel('catalog_layout_center_center_panel', {title:"Dashboard", url:Mage.url + '/mage_catalog/',loadOnce:true,autoCreate:true}));
                //Layout_Center.add('south', new Ext.ContentPanel('catalog_layout_center_south_panel', {autoCreate:true}));
                Layout_Center.endUpdate();
                
                Layout.beginUpdate();
                Layout.add('west', new Ext.NestedLayoutPanel(Layout_West));
                Layout.add('center', new Ext.NestedLayoutPanel(Layout_Center));
                Layout.endUpdate();
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"Products and Categories",closable:false}));
                Core_Layout.endUpdate();            
                loaded = true;
                
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },
        
        getLayout : function(name) {
            return this._layouts.get(name);
        },
        
        loadMainPanel : function() {
            this.init();
        }
    }
}();
