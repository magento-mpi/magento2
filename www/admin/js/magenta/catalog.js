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
                        initialSize : 200,
                        split:true,
                        autoScroll:false,
                        collapsible:true,
                        collapsedTitle : 'Categories Tree',
                        titlebar:true
                    },
                    center : {
                        autoScroll : true,
                        titlebar : false,
                        tabPosition : 'top',
                       	alwaysShowTabs : true
                    }
                });
                
                this._layouts.add('main', Layout);
                
                var Layout_West = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                        center: {
                            hideWhenEmpty : true,
                            autoScroll:false,
                            titlebar:false,
                            tabPosition : 'top'
                        }, 
                        south: {
                            hideWhenEmpty : true,
                            split:true,
                            initialSize:200,
                            minSize:50,
                            maxSize:400,
                            autoScroll:true,
                            titlebar : true,
                            collapsible:true
                         }
                     }
                );
                
                this._layouts.add('tree', Layout_West);
                
                Layout_West.beginUpdate();
                // Create tree
                Mage.Catalog_Category_Tree.create();
                Layout_West.endUpdate();
                
                Layout.beginUpdate();
                Layout.add('west', new Ext.NestedLayoutPanel(Layout_West, {title : 'Catalog'}));
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
