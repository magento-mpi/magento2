Mage.Catalog_Category = function(){
    var loaded = false;
    var Layout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        loadAttributesPanel: function() {
            
            var Core_Layout = Mage.Core.getLayout();
            if (!Layout) {
                Layout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    west: {
                        split:true,
                        autoScroll:true,
                        collapsible:false,
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
                            autoScroll:true,
                            titlebar:false
                        }, 
                        south: {
                            split:true,
                            initialSize:300,
                            minSize:50,
                            maxSize:400,
                            autoScroll:true,
                            collapsible:true
                         }
                     }
                );
                
                this._layouts.add('tree', Layout_West);
                
                Layout_West.beginUpdate();
                Layout_West.add('center', new Ext.ContentPanel('category_attr_set_panel', {
                        autoCreate:true,
                        url:Mage.url + '/mage_catalog/category/arrtibutesSetGrid'
                    }));
                Layout_West.add('south', new Ext.ContentPanel('category_attr_set_tree_panel', {
                        autoCreate:true,
                        url:Mage.url + '/mage_catalog/category/arrtibutesSetTree'
                    }));
                Layout_West.endUpdate();
                
                
                var Layout_Center = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                     center:{
                         autoScroll:true,
                         titlebar:false
                     },
                     south: {
                         split:true,
                         initialSize:300,
                         minSize:50,
                         maxSize:400,
                         autoScroll:true,
                         collapsible:true
                      }
                 });
                
                this._layouts.add('workZone', Layout_Center);
                
                Layout_Center.beginUpdate();
                Layout_Center.add('center', new Ext.ContentPanel('category_attributes_panel', {
                        title:"Dashboard",
                        loadOnce:true,
                        autoCreate:true,
                        url:Mage.url + '/mage_catalog/category/attributesGrid'
                    }));
                Layout_Center.add('south', new Ext.ContentPanel('category_attribute_form_panel', {autoCreate:true}));
                Layout_Center.endUpdate();
                
                Layout.beginUpdate();
                Layout.add('west', new Ext.NestedLayoutPanel(Layout_West));
                Layout.add('center', new Ext.NestedLayoutPanel(Layout_Center));
                Layout.endUpdate();
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"Category Attributes",closable:false}));
                Core_Layout.endUpdate();            
                loaded = true;
                
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },
        
        getLayout : function(name) {
            return this._layouts.get(name);
        },

        create: function() {
            
        }
    }
}();