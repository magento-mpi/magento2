Mage.Customer = function(depend){
    var loaded = false;
    var Layout = null;
    var gridLayout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!Layout) {
                Layout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs:true
                    },
                    south: {
                        split:true,
                        initialSize:200,
                        minSize:100,
                        maxSize:400,
                        autoScroll:true,
                        collapsible:true,
                        collapsedTitle : '<b>Customer info</b>',
                     }
                });
                
                this._layouts.add('main', Layout);
                
                this.initGrid();
                Layout.beginUpdate();
                Layout.add('center', new Ext.GridPanel(this.grid, {title:"test"}));
                Layout.add('south', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                Layout.endUpdate();
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"Customers",closable:false}));
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
        },

        initGrid: function(parentLayout){
            var dataRecord = Ext.data.Record.create([
                {name: 'customer_id', mapping: 'customer_id'},
                {name: 'customer_email', mapping: 'customer_email'},
                {name: 'customer_firstname', mapping: 'customer_firstname'},
                {name: 'customer_lastname', mapping: 'customer_lastname'}
            ]);
                
            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'customer_id'
            }, dataRecord);
                
             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: Mage.url + '/mage_customer/customer/gridData/'}),
                reader: dataReader,
                remoteSort: true
             });
                
            dataStore.setDefaultSort('customer_id', 'desc');
      

            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'customer_id'},
                {header: "Email", sortable: true, dataIndex: 'customer_email'},
                {header: "Firstname", sortable: true, dataIndex: 'customer_firstname'},
                {header: "Lastname", sortable: true, dataIndex: 'customer_lastname'}
            ]);
            
            var rowSelector = new Ext.grid.RowSelectionModel({singleSelect : true});
            var grid = new Ext.grid.Grid(Layout.getEl().createChild({tag: 'div'}), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                monitorWindowResize : true,
                autoHeight : true,
                selModel : rowSelector,
                enableColLock : false
            });
            
            rowSelector.on('rowselect', this.showItem.createDelegate(this));
            
            grid.render();
            grid.getDataSource().load({params:{start:0, limit:25}});            
            
            var gridHead = grid.getView().getHeaderPanel(true);
            var gridFoot = grid.getView().getFooterPanel(true);
           
            var paging = new Ext.PagingToolbar(gridHead, dataStore, {
                pageSize: 25,
                displayInfo: true,
                displayMsg: 'Displaying products {0} - {1} of {2}',
                emptyMsg: 'No products to display'                
            });
            
            paging.add('-', {
                text: 'Create New',
                cls: 'x-btn-text-icon product_new'
                //handler : this.createItem.createDelegate(this)
            },{
                text: 'Add Filter',
                //handler : this.addFilter,
                scope : this,
                cls: 'x-btn-text-icon'
            },{
                text: 'Apply Filters',
                //handler : this.applyFilters,
                scope : this,
                cls: 'x-btn-text-icon'
            });
            
            this.grid = grid;
            return grid;
        },

        showItem: function(row){
            
        }
    }
}();
