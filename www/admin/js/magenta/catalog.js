Mage.Catalog = function(depend){
    var loaded = false;
    var Layout = null;
    return {
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
                
                var Layout_West = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                        center: {
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
                
                Layout_West.beginUpdate();
                Layout_West.add('center', new Ext.ContentPanel('catalog_main_left_tree_panel', {url: Mage.url + '/mage_catalog/tree', autoCreate:true}));
                Layout_West.add('south', new Ext.ContentPanel('catalog_main_left_preview_panel', {autoCreate:true}));
                Layout_West.endUpdate();
                
                
                var Layout_Center = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    north: {
                        split:true,
                        initialSize:200,
                        minSize:50,
                        maxSize:400,
                        autoScroll:true,
                        collapsible:true
                     },
                     center:{
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
                 });
                
                
                Layout_Center.beginUpdate();
                Layout_Center.add('north', new Ext.ContentPanel('catalog_layout_center_north_panel', {autoCreate:true}));
                Layout_Center.add('center', new Ext.ContentPanel('catalog_layout_center_center_panel', {title:"Dashboard", url:Mage.url + '/mage_catalog/',loadOnce:true,autoCreate:true}));
                Layout_Center.add('south', new Ext.ContentPanel('catalog_layout_center_south_panel', {autoCreate:true}));
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
        
        loadMainPanel : function() {
            this.init();
        }
    }
}();
