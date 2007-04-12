Mage.Catalog_Product_Attributes = function(){
    var loaded = false;
    var Layout = null;
    return {

        westLayout : null,
        btnEdit : null,
        btnDelete : null,

        attributeGrid : null,
        attributeGridToolbar : null,
        attributeGridUrl : Mage.url + '/mage_catalog/product/attributeList/',

        setGrid : null,
        setGridUrl :  Mage.url + '/mage_catalog/product/attributeSetList/',

        editSetGrid : null,
        editSetGridUrl : Mage.url + '/mage_catalog/product/attributesetproperties/',

        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!Layout) {
                Layout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    west: {
                        split:true,
                        initialSize : 300,
                        autoScroll:true,
                        collapsible:false,
                        titlebar:false
                    },
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs : false,
                        tabPosition : 'top'
                    }
                });



                this.westLayout = new Ext.BorderLayout(Layout.getRegion('west').getEl().createChild({tag:'div'}), {
                    center: {
                        split:true,
                        autoScroll:true,
                        collapsible:false,
                        titlebar:false
                    },
                    south : {
                        hideWhenEmpty : true,
                        initialSize : 200,
                        autoScroll : true,
                        collapsible:false,
                        titlebar : false,
                        hideTabs : true
                    }
                });

                this.initSetGrid();
                this.westLayout.beginUpdate();
                this.westLayout.add('center', new Ext.GridPanel(this.setGrid));
                this.westLayout.endUpdate();


                Layout.beginUpdate();
                Layout.add('west', new Ext.NestedLayoutPanel(this.westLayout));
                Layout.endUpdate();

                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"Product Attributes",closable:false}));
                Core_Layout.endUpdate();
            } else {
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },

        loadAttributeGrid : function(setId) {
            if (this.attributeGrid == null) {
                this.initAttributesGrid(setId);
                Layout.beginUpdate();
                Layout.add('center', new Ext.GridPanel(this.attributeGrid));
                Layout.endUpdate();
            } else {
                this.attributeGrid.getDataSource().proxy.getConnection().url = Mage.url + '/mage_catalog/product/attributeList/set/'+setId+'/';
                this.attributeGrid.getDataSource().load({params:{start:0, limit:10}});
            }
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
                proxy: new Ext.data.HttpProxy({url: this.setGridUrl}),
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

            this.setGrid = new Ext.grid.Grid(Ext.DomHelper.append(Layout.getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                loadMask : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                autoSizeColumns: true,
                monitorWindowResize: true,
                enableColLock : false
            });

            this.setGrid.on('rowclick', this.onRowClick.createDelegate(this));
            this.setGrid.getSelectionModel().on('rowselect', function(){
                    this.btnEdit.enable();
                    this.btnDelete.enable();
                }.createDelegate(this)
            );

            this.setGrid.render();

            var gridHead = this.setGrid.getView().getHeaderPanel(true);
            var tb = new Ext.Toolbar(gridHead);
            tb.addButton ({
                text: 'Add',
                handler : this.onAdd.createDelegate(this)
            });

            this.btnEdit = tb.addButton({
                text: 'Edit',
                enableToggle : true,
                handler : this.onEditToogle.createDelegate(this),
                disabled : true
            });

            this.btnDelete = tb.addButton ({
                text: 'Delete',
                disabled : true
            });
            this.setGrid.getDataSource().load({params:{start:0, limit:10}});
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
                id: 'attribute_id'
            }, dataRecord);

            var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.attributeGridUrl +  'set/'+setId+'/'}),
                reader: dataReader,
                remoteSort: true
            });

            dataStore.setDefaultSort('attribute_code', 'asc');


            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'attribute_id'},
                {header: "Code", sortable: true, dataIndex: 'attribute_code'},
                {header: "Input type", sortable: true, dataIndex: 'data_input'},
                {header: "Data type", sortable: true, dataIndex: 'data_type'},
                {header: "Required", sortable: true, dataIndex: 'required'}
            ]);

            this.attributeGrid = new Ext.grid.Grid(Ext.DomHelper.append(Layout.getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                loadMask : true,
                autoSizeColumns : true,
                monitorWindowResize : true,
                autoHeight : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                enableColLock : false
            });

            this.attributeGrid.render();
            this.attributeGrid.getDataSource().load({params:{start:0, limit:10}});
        },

        loadMainPanel : function() {
            this.init();
        },

        loadEditSetGrid : function(setId) {
            if (this.editSetGrid == null) {
                this.initEditSetGrid(setId);
                this.westLayout.beginUpdate();
                this.westLayout.add('south', new Ext.GridPanel(this.editSetGrid));
                this.westLayout.endUpdate();
            } else {
                this.editSetGrid.getDataSource().proxy.getConnection().url = this.editSetGridUrl +  '/set/'+setId+'/';
                this.editSetGrid.getDataSource().load();
            }
        },

        initEditSetGrid : function(setId) {

            var dataRecord = Ext.data.Record.create([
                {name: 'id', mapping: 'id'},
                {name: 'name', mapping: 'name'},
                {name: 'value', mapping: 'value'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'name'
            }, dataRecord);

            var dataStore = new Ext.data.Store({
                proxy : new Ext.data.HttpProxy({url: this.editSetGridUrl +  'set/'+setId+'/'}),
                reader : dataReader,
                remoteSort: true
            });

            dataStore.setDefaultSort('id', 'asc');

            var colModel = new Ext.grid.ColumnModel([
                {header: "#ID",  width : 60, locked:true, dataIndex: 'id'},
                {header: "Name",  width : 60, locked:true, dataIndex: 'name'},
                {header: "Value", width : 40, locked:true, dataIndex: 'value'}
            ]);

            this.editSetGrid = new Ext.grid.Grid(Ext.DomHelper.append(this.westLayout.getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                loadMask : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                autoSizeColumns: true,
                monitorWindowResize: true,
                enableColLock : false
            });

            this.editSetGrid.render();
            this.editSetGrid.getDataSource().load();
        },

        onAdd : function() {
            this.btnEdit.enable();
            this.btnEdit.toggle(true);
            this.westLayout.getRegion('south').show();
            this.loadEditSetGrid(0);
        },

        onEditToogle : function(btn, e) {

            if (btn.pressed == true) {
                try {
                    row = this.setGrid.getSelectionModel().getSelected();
                    setId = row.id;
                } catch(e){
                    alert(e);
                };

                if (setId) {
                    this.loadEditSetGrid(setId);
                    this.westLayout.getRegion('south').show();
                } else {
                    alert('error');
                }
            } else {
                this.westLayout.getRegion('south').hide();
            }
        },


        onRowClick : function(grid, rowIndex, e) {
            var setId = 0;
            try {
                setId =  this.setGrid.getDataSource().getAt(rowIndex).id;
            } catch (e){};

            if (setId) {
                if (this.btnEdit.pressed == true) {
                    this.loadEditSetGrid(setId);
                } else {
                    this.loadAttributeGrid(setId)
                }
            } else {
                return false;
            }
            e.stopEvent();
        }

    }
}();

