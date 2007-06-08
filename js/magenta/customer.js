Mage.Customer = function(depend){
    var loaded = false;
    var Layout = null;
    var gridLayout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        baseLayout:null,
        customerLayout: null,
        editLayout : null,
        addressLayout : null,
        addressView : null,
        gridPanel:null,
        grid:null,
        addressLoading : null,
        addressPanel : null,
        addressViewUrl : Mage.url + 'address/gridData/',
        addressViewForm : Mage.url + 'address/form/',
        deleteAddressUrl : Mage.url + 'address/delete/',

        customerCardUrl : Mage.url + 'customer/card/',
        customerGridDataUrl : Mage.url + 'customer/gridData/',
        deleteUrl : Mage.url + 'customer/delete/',
        formPanels : new Ext.util.MixedCollection(),
        forms2Panel : new Ext.util.MixedCollection(),
        forms : new Ext.util.MixedCollection(),
        customerCardId : null,
        lastSelectedCustomer : null,
        
        tabCollections : new Ext.util.MixedCollection(),

        formsEdit : [],

        init : function() {
            var Core_Layout = Mage.Admin.getLayout();
            if (!this.baseLayout) {
                this.baseLayout = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                     center:{
                         titlebar: false,
                         autoScroll: true,
                         resizeTabs : true,
                         hideTabs : true,
                         tabPosition: 'top'
                     }
                });

                this.customerLayout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    north: {
                        hideWhenEmpty : true,
                        titlebar : false,
                        split : true,
                        initialSize:21,
                        minSize:21,
                        maxSize:200,
                        autoScroll:true,
                        collapsible:false
                    },
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs:true,
                        preservPanels : true
                    },
                    south : {
                        preservePanels : true,
                        hideWhenEmpty : true,
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
                this.baseLayout.add('center', new Ext.NestedLayoutPanel(this.customerLayout));
                this.baseLayout.endUpdate();

                Core_Layout.add('center', new Ext.NestedLayoutPanel(this.baseLayout, {title : 'Mange Customers'}));
                
                this.customerCard = new Mage.core.ItemCard({
                    region : this.customerLayout.getRegion('south'),
                    url : Mage.url + 'customer/card/id/'
                });
                
                this.customerCard.toolbarAdd(new Ext.ToolbarButton({
                    text : 'Delete Customer'
                }));

            } else { // not loaded condition
                Mage.Admin.getLayout().getRegion('center').showPanel(this.baseLayout);
            }
        },

        viewGrid: function(){
            this.init();
            if (!this.gridPanel){
                var grid = this.initGrid();
                this.customerLayout.beginUpdate();
                this.gridPanel = this.customerLayout.add('center',new Ext.GridPanel(grid));
                this.customerLayout.endUpdate();
                this.grid.getDataSource().load({params:{start:0, limit:25}});
            }
        },

        getLayout : function(name) {
            return this._layouts.get(name);
        },

        loadMainPanel : function() {
            this.viewGrid();
        },

        initGrid: function(parentLayout){
            var dataRecord = Ext.data.Record.create([
                {name: 'customer_id', mapping: 'customer_id'},
                {name: 'email', mapping: 'email'},
                {name: 'firstname', mapping: 'firstname'},
                {name: 'lastname', mapping: 'lastname'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'customer_id'
            }, dataRecord);

             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.customerGridDataUrl}),
                reader: dataReader,
                remoteSort: true
             });

            dataStore.setDefaultSort('customer_id', 'desc');


            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'customer_id'},
                {header: "Email", sortable: true, dataIndex: 'email'},
                {header: "Firstname", sortable: true, dataIndex: 'firstname'},
                {header: "Lastname", sortable: true, dataIndex: 'lastname'}
            ]);

            var rowSelector = new Ext.grid.RowSelectionModel({singleSelect : true});
            var grid = new Ext.grid.Grid(this.customerLayout.getEl().createChild({tag: 'div'}), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                monitorWindowResize : true,
                autoHeight : true,
                loadMask: true,
                selModel : rowSelector,
                enableColLock : false
            });
            
            grid.getSelectionModel().on('rowselect', this.loadCustomer.createDelegate(this));

            this.grid = grid;

            this.grid.render();

            var gridHead = this.grid.getView().getHeaderPanel(true);
            var gridFoot = this.grid.getView().getFooterPanel(true);

            var paging = new Ext.PagingToolbar(gridHead, this.grid.getDataSource(), {
                pageSize: 25,
                displayInfo: true,
                displayMsg: 'Displaying customers {0} - {1} of {2}',
                emptyMsg: 'No customers to display'
            });

            paging.add('-', {
                text: 'Create New',
                cls: 'x-btn-text-icon btn-add-user',
                handler : this.createCustomer,
                scope : this
            });
            return grid;
        },
    
        loadCustomer : function(sm, rowIndex, record) {
            this.customerCard.loadRecord(record);
        },
        
        createCustomer : function(btn, event) {
            this.wizard = new Mage.Wizard(Ext.DomHelper.append(document.body, {tag : 'div'}, true), {
                points : [{
                    url : Mage.url + 'test/wizard/',
                    help : 'hidden'
                },{
                    url : Mage.url + 'test/wizard/step/1/',
                    finish : 'hidden',
                    help : 'hidden'
                },{
                    url : Mage.url + 'test/wizard/step/2/',
                    finish : 'enable',
                    help : 'hidden'
                }]
            });    
            this.wizard.show(btn.getEl());
        }
    }
}();
