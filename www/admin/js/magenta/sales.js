Mage.Sales = function(depend){
    return {
        layout : null,
        centerLayout : null,
        
        webSiteTree : null,
        grid : null,
        gridPageSize : 30,
        gridUrl : Mage.url + 'mage_sales/order/grid/',
        lastSelectedRecord : null,
        
        formPanel : null,
        formUrl : Mage.url + 'mage_sales/order/form/',
        
        oTree : null,
        websiteCBUrl : Mage.url + 'mage_core/website/list/',
        websitesTreeUrl : Mage.url + 'mage_sales/order/tree/',
        
        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!this.layout) {
                this.layout =  new Ext.BorderLayout(Core_Layout.getEl().createChild({tag:'div'}), {
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs : true
                    },
                    west: {
                        split:true,
                        initialSize:200,
                        minSize:100,
                        maxSize:400,
                        autoScroll:false,
                        collapsible:true,
                        hideTabs:true
                     }
                });
                
                this.centerLayout = new Ext.BorderLayout(Core_Layout.getEl().createChild({tag:'div'}), {
                     center:{
                         titlebar: false,
                         autoScroll:false,
                         resizeTabs : true,
                         hideTabs : true,
                         tabPosition: 'top'
                     },
                     south : {
                         hideWhenEmpty : true,
                         split:true,
                         initialSize:300,
                         minSize:50,
                         titlebar: true,
                         autoScroll: true,
                         collapsible: true,
                         hideTabs : true
                     }
                 });
                 
                this.centerLayout.beginUpdate();
                this.initGrid({
                    baseEl : this.centerLayout.getRegion('center').getEl().createChild({tag:'div'})
                })
                
                this.centerLayout.add('center', new Ext.GridPanel(this.grid, {
                    fitToFrame:true
                }));

                this.centerLayout.endUpdate();

                this.layout.beginUpdate();
                this.layout.add('center', new Ext.NestedLayoutPanel(this.centerLayout, {
                    autoCreate : true,
                    fitToFrame:true
                }));
                this.layout.add('west', new Ext.ContentPanel(Ext.id(), {
                    autoCreate : true,
                    fitToFrame:true
                }));
                this.layout.endUpdate();
                
                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(this.layout, {title:"Orders", closable:false}));
                Core_Layout.endUpdate();            
                
                this.loadWebSitesTree();
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(this.layout);
            }
        },
        
        loadMainPanel : function() {
            this.init();

        },
        
        loadWebSitesTree : function() {
            this.initWebSiteTree();            
        },
        
        initWebSiteTree : function() {
            var layoutEl = this.layout.getEl();
            if (!layoutEl) {
                return false;
            }
            
            panelEl = layoutEl.createChild({children:[{id:'tree-tb'},{id:'tree-body'}]});
            var tb = new Ext.Toolbar('tree-tb');
            
            var panel = this.layout.add('west', new Ext.ContentPanel(panelEl, {
                fitToFrame : true,
                autoScroll:true,
                resizeEl : panelEl,
                toolbar : tb
            }))
            
            this.oTree = new Ext.tree.TreePanel(panel.getEl().createChild({id:Ext.id()}),{
                animate:true,
                enableDD:true,
                containerScroll: true,
                lines:false,
                rootVisible:false,
                loader: new Ext.tree.TreeLoader()
            });
            
            var sm = this.oTree.getSelectionModel();
            sm.on('selectionchange', function(){
                var node = sm.getSelectedNode();
                var data = {};
                var url = this.gridUrl;
                if (node.attributes.siteId) {
                    url  =  url + 'siteid/'+ node.attributes.siteId + '/'
                }
                if (node.attributes.orderStatus) {
                   url  =  url + 'orderstatus/'+ node.attributes.orderStatus + '/'    
                }
                this.grid.getDataSource().proxy.getConnection().url = url
                this.grid.getDataSource().load();
            }.createDelegate(this));
            
            var wsRoot = new Ext.tree.AsyncTreeNode({
                allowDrag:true,
                allowDrop:true,
                id:'wsroot',
                text:'WebSites',
                cls:'wsroot',
                loader:new Ext.tree.TreeLoader({
                    dataUrl: this.websitesTreeUrl
                })
            });
                
            this.oTree.setRootNode(wsRoot);
            this.oTree.render();
            wsRoot.expand();            
        },
        
        initGrid : function(config) {
            if (!config.baseEl) {
                return false;
            }
            
            var baseEl = config.baseEl;

            this.dataRecord = Ext.data.Record.create([
                {name: 'real_order_id', mapping: 'real_order_id'},
                {name: 'customer_id', mapping: 'customer_id'},
                {name: 'firstname', mapping: 'firstname'},
                {name: 'lastname', mapping: 'lastname'},
                {name: 'grand_total', mapping: 'grand_total'},
                {name: 'status', mapping: 'status'},
                {name: 'created_at', mapping: 'created_at'},
                {name: 'website_id', mapping: 'website_id'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'real_order_id'
            }, this.dataRecord);

             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.gridUrl}),
                reader: dataReader,
                baseParams : {pageSize : this.gridPageSize},
                remoteSort: true
             });

            var colModel = new Ext.grid.ColumnModel([
                {header: "Order ID", sortable: true, dataIndex: 'real_order_id'},
                {header: "Customer ID", sortable: true, dataIndex: 'customer_id'},
                {header: "Firstname", sortable: true, dataIndex: 'firstname'},
                {header: "Lastname", sortable: true, dataIndex: 'lastname'},
                {header: "Grand total", sortable: true, dataIndex: 'grand_total'},
                {header: "Status", sortable: true, dataIndex: 'status'},
                {header: "Created at", sortable: true, dataIndex: 'created_at'},
                {header: "Website", sortable: true, dataIndex: 'website_id'}
            ]);

            this.grid = new Ext.grid.Grid(baseEl, {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                loadMask: true,
                monitorWindowResize : true,
                autoHeight : false,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : false}),
                enableColLock : false
            });

            this.grid.getSelectionModel().on('rowselect', function(sm, rowIndex, record){
                if (this.lastSelectedRecord !== record) {
                    this.lastSelectedRecord = record;
                    this.loadOrder({
                        id : record.data.real_order_id
                    });
                }
            }, this)
            
            this.grid.render();
            
            var gridHead = this.grid.getView().getHeaderPanel(true);
            var gridFoot = this.grid.getView().getFooterPanel(true);

            var paging = new Ext.PagingToolbar(gridHead, this.grid.getDataSource(), {
               pageSize: this.gridPageSize,
               displayInfo: true,
               displayMsg: 'Orders {0} - {1} of {2}',
               emptyMsg: 'No orders to display'
            });
            
            paging.items.map.item5.enable();
        },
        
        loadOrder : function(config) {
            console.log(config);
            this.createEditPanel(config.id || 0);
        },
        
        createEditPanel : function(order_id) {
            var baseEl = this.centerLayout.getRegion('south').getEl().createChild({tag : 'div'});
            var layout = new Ext.BorderLayout(baseEl,{
                north : {
                    autoScroll:false
                },
                center : {
                    
                }
            });
            
            var layoutBaseEl = layout.getEl().createChild({tag : 'div'});
            var toolbar = this.createFormToolbar({
                baseEl : layoutBaseEl.createChild({tag : 'div'})
            });
            layout.add('north', new Ext.ContentPanel(layoutBaseEl, {
                autoCreate : true,
                toolbar : toolbar
            }));
            
            this.formPanel = layout.add('center', new Ext.ContentPanel(Ext.id(), {
                autoCreate : true,
                url : this.formUrl,
                method : 'POST',
                params : {real_order_id : order_id}
            }));
            
            this.centerLayout.add('south', new Ext.NestedLayoutPanel(layout, {
                title : 'Order Info'
            }));
        },
        
        createFormToolbar : function(config) {
            this.toolbar = new Ext.Toolbar(config.baseEl);
            this.toolbar.add(new Ext.ToolbarButton({
                text : 'Save'
            }));
            return this.toolbar;
        }
    }
}();
