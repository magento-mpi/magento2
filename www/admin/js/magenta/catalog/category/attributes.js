Mage.Catalog_Category_Attributes = function(){
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
                        titlebar:false
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
                
                this._layouts.add('left', Layout_West);
                
                Layout_West.beginUpdate();
                Layout_West.add('center', new Ext.ContentPanel('category_attr_set_panel', {
                        autoCreate:true
                    }));
                Layout_West.add('south', new Ext.ContentPanel('category_attr_set_tree_panel', {
                        autoCreate:true,
                        url:Mage.url + 'mage_catalog/category/arrtibutesSetTree/'
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
                        url:Mage.url + 'mage_catalog/category/attributesGrid/'
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
                this.initAttrSetGrid();
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },
        
        initAttrSetGrid: function(){
            var parentElement = this.getLayout('left').getRegion('center');
            var gColumn = new Ext.grid.ColumnModel([{
                   header: "Attributes Set Name",
                   dataIndex: 'name',
                   editor: new Ext.grid.GridEditor(new Ext.form.TextField({allowBlank: false}))
                }]);
            
            gColumn.defaultSortable = true;
            
            var dataRecord = Ext.data.Record.create([
                   {name: 'category_attribute_set_id', type: 'int'},
                   {name: 'category_attribute_set_code', type: 'string'}
            ]);
            
            var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: Mage.url+'mage_catalog/category/attributesSetGridData/'}),
                reader: new Ext.data.JsonReader({
                       record: 'items'
                   }, dataRecord)
            });

            var grid = new Ext.grid.EditorGrid(Ext.DomHelper.append(this.getLayout('left').getRegion('center').getEl(), {tag: 'div'}), {
                ds: dataStore,
                cm: gColumn,
                enableColLock:false
            });

            var gridLayout = Ext.BorderLayout.create({
                    center: {
                        margins:{left:3,top:3,right:3,bottom:3},
                        panels: [new Ext.GridPanel(grid)]
                    }
                }, Ext.DomHelper.append(this.getLayout('left').getRegion('center').getEl(), {tag: 'div'}));
            
            grid.render();
            var gridHead = grid.getView().getHeaderPanel(true);
            var tb = new Ext.Toolbar(gridHead, [{
                text: 'Add set'
            }]);
            dataStore.load();
        },
        
        getLayout : function(name) {
            return this._layouts.get(name);
        },

        create: function() {
            
        }
    }
}();