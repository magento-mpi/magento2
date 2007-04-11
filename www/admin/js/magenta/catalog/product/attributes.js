Mage.Catalog_Product_Attributes = function(){
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
                        collapsible:false,
                        titlebar:false
                    },
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs : false,
                        tabPosition : 'top'
                    },
                    east : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs:true
                    }
                    
                });
                this._layouts.add('main', Layout);
                
  //              var attributesGrid = this.initAttributesGrid();                
                var setGrid = this.initSetGrid();
                
                Layout.beginUpdate();
                Layout.add('west', new Ext.GridPanel(setGrid));
                Layout.endUpdate();
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"Product Attributes",closable:false}));
                Core_Layout.endUpdate();            
                loaded = true;
                
            } else {
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },
        
        loadAttributeGrid : function(setId) {
            var attributesGrid = this.initAttributesGrid(setId);                
               
            Layout.beginUpdate();
            Layout.add('center', new Ext.GridPanel(attributesGrid));
            Layout.endUpdate();
        },
        
        initSetGrid : function() {
            var dataRecord = Ext.data.Record.create([
                {name: 'id', mapping: 'product_attribute_set_id'},
                {name: 'name', mapping: 'product_set_code'}
            ]);
                
            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'product_attribute_set_id'
            }, dataRecord);
                
             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: Mage.url + '/mage_catalog/product/attributeSetList/'}),
                reader: dataReader,
                remoteSort: true
             });
                
            dataStore.setDefaultSort('product_id', 'desc');
      

            var colModel = new Ext.grid.ColumnModel([{
                header: "Set Code", 
                sortable: false, 
                dataIndex: 'name',
                editor: new Ext.grid.GridEditor(new Ext.form.TextField({
                     allowBlank: false
                }))
            }]);

            var Set = Ext.data.Record.create([
               {name: 'name', type: 'string'},
           ]);

            var grid = new Ext.grid.EditorGrid(Ext.DomHelper.append(Layout.getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns: true,
                monitorWindowResize: true,                
                enableColLock : false
            });
            
            grid.on('rowdblclick', this.onRowDblClick.createDelegate(this));
            
            grid.render();
            
            var gridHead = grid.getView().getHeaderPanel(true);
            var tb = new Ext.Toolbar(gridHead, [{
                text: 'Add',
                handler : function(){
                    var p = new Set({
                        name: 'New Set'
                    });
                    grid.stopEditing();
                    dataStore.insert(0, p);
                    grid.startEditing(0, 0);
                }
            },{
                text: 'Edit'
            },{
                text: 'Save'
            },{
                text: 'Delete'
            }]);            
            grid.getDataSource().load({params:{start:0, limit:10}});            
            
            return grid;
        },
        
        onRowDblClick : function(grid, rowIndex, e) {
            var setId = 0;
            try {
                setId = grid.getDataSource().getAt(rowIndex).id;
            } catch (e) {
                alert(e);
            }
            if (setId) {
                this.loadAttributeGrid(setId) 
            } else {
                return false;
            }
            e.stopEvent();
        },
        
        initAttributesGrid : function(setId) {
            if (!setId) {
                return false;
            }
            var dataRecord = Ext.data.Record.create([
                {name: 'attribute_id', mapping: 'attribute_id'},
                {name: 'attribute_code', mapping: 'attribute_code'},
                {name: 'data_input', mapping: 'data_input'},
                {name: 'data_type', mapping: 'data_type'},
                {name: 'required', mapping: 'required'}
            ]);
                
            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'product_id'
            }, dataRecord);
                
             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: Mage.url + '/mage_catalog/product/attributeList/set/'+setId+'/'}),
                reader: dataReader,
                remoteSort: true
             });
                
            dataStore.setDefaultSort('product_id', 'desc');
      

            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'attribute_id'},
                {header: "Code", sortable: true, dataIndex: 'attribute_code'},
                {header: "Input type", sortable: true, dataIndex: 'data_input'},
                {header: "Data type", sortable: true, dataIndex: 'data_type'},
                {header: "Required", sortable: true, dataIndex: 'required'}
            ]);

            var grid = new Ext.grid.Grid(Ext.DomHelper.append(Layout.getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                monitorWindowResize : true,
                autoHeight : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                enableColLock : false
            });
            
            
            
            grid.render();
            grid.getDataSource().load({params:{start:0, limit:10}});            
            
            return grid;
                
        },
        
        loadMainPanel : function() {
            this.init();
        }
    }
}();
