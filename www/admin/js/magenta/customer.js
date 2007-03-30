Mage.Customer = function(depend){
    var loaded = false;
    var Layout = null;
    var gridLayout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        baseLayout:null,
        customerLayout: null,
        gridPanel:null,
        grid:null,
        
        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!this.baseLayout) {
                this.baseLayout = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                     center:{
                         titlebar: true,
                         autoScroll:true,
                         resizeTabs : true,
                         hideTabs : true,
                         tabPosition: 'top'
                     },
                });
                
                this.customerLayout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    north: {
                        titlebar:true,
                        split:true,
                        initialSize:83,
                        minSize:0,
                        maxSize:200,
                        autoScroll:true,
                        collapsible:true
                    },
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs:true
                    },
                    south: {
                         split:true,
                         initialSize:300,
                         minSize:50,
                         titlebar: true,
                         autoScroll:true,
                         collapsible:true,
                         hideTabs : true
                     }
                });
                
                this.baseLayout.beginUpdate();
                this.baseLayout.add('center', new Ext.NestedLayoutPanel(this.customerLayout, {title:'Manage Customers'}));
                this.baseLayout.endUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(this.baseLayout, {title : 'Products Grid'}));
                
                this.viewGrid();
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(this.baseLayout);
            }
        },

        viewGrid: function(){
            if (!this.gridPanel){
                var grid = this.initGrid();
                this.customerLayout.beginUpdate();
                this.gridPanel = this.customerLayout.add('center',new Ext.GridPanel(grid));
                this.customerLayout.endUpdate();
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
            var grid = new Ext.grid.Grid(this.customerLayout.getEl().createChild({tag: 'div'}), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                monitorWindowResize : true,
                autoHeight : true,
                selModel : rowSelector,
                enableColLock : false
            });
            
            grid.on('click', this.showEditPanel.createDelegate(this));
            
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
                cls: 'x-btn-text-icon product_new',
                handler : this.createItem.createDelegate(this)
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
        
        createItem: function(){
            this.showEditPanel(false);
        },

        showEditPanel: function(row){
            customerId = false;
            var title = 'New Customer';
            Ext.dump(arguments.length);
            //if (row){
              //  var title = 'Edit Customer #';
                //Ext.dump(row);
                //customerId = this.grid.getDataSource().getAt(row).id;
//            }
            
             // title for form layout            

            if (this.customerLayout.getRegion('south').getActivePanel()) {
                this.customerLayout.getRegion('south').clearPanels();
                this.editablePanels = [];
            }
            
           /* if (rowId >= 0) {
             try {
                  prodId = this.grid.getDataSource().getAt(rowId).id;
                  title = 'Edit: ' + this.grid.getDataSource().getById(prodId).get('name');
              } catch (e) {
                  Ext.MessageBox.alert('Error!', e.getMessage());
              }
            }*/
            this.editPanel = new Ext.BorderLayout(this.customerLayout.getEl().createChild({tag:'div'}), {
                    hideOnLayout:true,
                    north: {
                        split:false,
                        initialSize:28,
                        minSize:28,
                        maxSize:28,
                        autoScroll:false,
                        titlebar:false,                        
                        collapsible:false
                     },
                     center:{
                         autoScroll:true,
                         titlebar:false,
                         resizeTabs : true,
                         tabPosition: 'top'
                     }
            });

            this.editPanel.add('north', new Ext.ContentPanel(this.customerLayout.getEl().createChild({tag:'div'})));

            this.customerLayout.beginUpdate();
            var failure = function(o) {Ext.MessageBox.alert('Product Card',o.statusText);}
            var cb = {
                //success : this.loadTabs.createDelegate(this),
                failure : failure
                //argument : {prod_id: prodId}
            };
            //var con = new Ext.lib.Ajax.request('GET', Mage.url + '/mage_catalog/product/card/product/'+prodId+'/setid/'+setId+'/typeid/'+typeId+'/', cb);  
            
            this.customerLayout.add('south', new Ext.NestedLayoutPanel(this.editPanel, {closable: true, title:title}));
            //this.customerLayout.getRegion('south').on('panelremoved', this.onRemovePanel.createDelegate(this));
            this.customerLayout.endUpdate();
        }
    }
}();
