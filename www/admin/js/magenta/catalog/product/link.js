Mage.Catalog_Product_RelatedPanel = function(){
    return{
        config : null,
        cPanel : null,
        grid : null,
        dataRecord : null,
        // mage_catalog/product/relatedList/product/:id
        // mage_catalog/product/bundleList/product/:id
        // mage_catalog/product/superList/product/:id
        gridUrl : Mage.url + 'mage_catalog/product/relatedList/',
        gridPageSize : 30,
        productSelector : null,
        
        create : function(config) {
            this.config = config;
            
            if (!config.panel || !config.tabInfo) {
                return false;
            }
            
            Ext.apply(this, config);
            
            var baseEl = this.panel.getRegion('center').getEl().createChild({tag:'div', id:'productCard_' + this.tabInfo.name});

            this.cPanel = new Ext.ContentPanel(baseEl, {
                            title : this.tabInfo.title || 'Related products',
                            closable : false,
                            url : this.tabInfo.url,
                            loadOnce: true,
                            background: true
                        });
                        
           this.panel.getRegion('center').on('beforeremove', function(region, panel, e){
                if (this.cPanel === panel) {
                    if (this.grid) {
                        this.grid.destroy(true);
                    }
                }
            }.createDelegate(this));            
            
            var um = this.cPanel.getUpdateManager();
            um.on('update', this.onUpdate.createDelegate(this));
            return this.cPanel;            
        },

        onUpdate : function() {
            var div = Ext.DomQuery.selectNode('div#relation_tab', this.cPanel.getEl().dom);    
            if (div) {
                this.initGrid({
                    baseEl : Ext.get(div)
                });
            
                this.productSelector = new Mage.Catalog_Product_ProductSelect({
                    gridUrl : Mage.url + 'mage_catalog/product/gridData/category/1/',
                    parentGrid : this.grid,
                    dataRecord : this.dataRecord
                });
            
                this.buildGridToolbar();    
                this.loadGrid();        
            }
        },        
        
        initGrid : function(config) {
            if (!config.baseEl) {
                return false;
            }
            
            var baseEl = config.baseEl;

            this.dataRecord = Ext.data.Record.create([
                {name: 'id', mapping: 'product_id'},
                {name: 'name', mapping: 'name'},
                {name: 'price', mapping: 'price'},
                {name: 'description', mapping: 'description'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'product_id'
            }, this.dataRecord);

             var productId = Mage.Catalog_Product.productsGrid.getSelectionModel().selections.items[0].id;

             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.gridUrl + 'product/' + productId + '/'}),
                reader: dataReader,
                baseParams : {pageSize : this.gridPageSize},
                remoteSort: true
             });

            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'id'},
                {header: "Name", sortable: true, dataIndex: 'name'},
                {header: "Price", sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
                {header: "Description", sortable: false, dataIndex: 'description'}
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

            var resizeBaseEl = new Ext.Resizable(baseEl, {
                wrap:true,
                pinned:true,
                width:540,
                height:200,
                minWidth:200,
                minHeight: 50,
                dynamic: false
            });
            resizeBaseEl.on('resize', this.grid.autoSize, this.grid);

            this.grid.render();
        },
        
        buildGridToolbar : function() {
            
            var gridHead = this.grid.getView().getHeaderPanel(true);
            var gridFoot = this.grid.getView().getFooterPanel(true);

            var paging = new Ext.PagingToolbar(gridHead, this.grid.getDataSource(), {
                pageSize: this.gridPageSize,
                displayInfo: true,
                displayMsg: 'Displaying products {0} - {1} of {2}',
                emptyMsg: 'No items to display'
            });

            paging.insertButton(0, new Ext.Toolbar.Separator({
            }));

            
            paging.insertButton(0, new Ext.ToolbarButton({
                text : 'Delete',
                handler : function(){
                    Ext.MessageBox.confirm('Warning','Are you sure ?', function(btn, text){
                        if (btn == 'yes') {
                            var sm = this.grid.getSelectionModel();
                            while(sm.selections.items.length) {
                                this.grid.getDataSource().remove(sm.selections.items[0]);
                            }
                        }
                    }.createDelegate(this))
                }.createDelegate(this)
            }));
            
            paging.insertButton(0, new Ext.ToolbarButton({
                text : 'Select Product',
                handler : this.productSelector.show,
                scope : this.productSelector
            }));
        },
        
        loadGrid : function() {
            this.grid.getDataSource().load();
        }
        
        
    }
}();

Mage.Catalog_Product_BundlePanel = function(){
    return {
        create: function(panel, tabInfo){
        
        }
    }
}();

Mage.Catalog_Product_SuperPanel = function(){
    return {
        create : function(panel, tabInfo){
        
        }
    }
}();