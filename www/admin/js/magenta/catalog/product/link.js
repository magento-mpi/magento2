Mage.Catalog_Product_RelatedPanel = function(){
    return{
        config : null,
        cPanel : null,
        grid : null,
        gridUrl : Mage.url + 'mage_catalog/product/gridData/category/1/',
        gridPageSize : 30,
        productSelector : null,
        
        create : function(config) {
            this.config = config;
            
            if (!config.panel || !config.tabInfo) {
                return false;
            }
            
            Ext.apply(this, config);
            
            var baseEl = this.panel.getRegion('center').getEl().createChild({tag:'div', id:'productCard_' + this.tabInfo.name});
            //var tb = new Ext.Toolbar(baseEl.createChild({tag:'div'}));

            this.cPanel = new Ext.ContentPanel(baseEl, {
                            title : this.tabInfo.title || 'Related products',
                            closable : false,
                            url : this.tabInfo.url,
                            loadOnce: true,
                            background: true
                        });

            var um = this.cPanel.getUpdateManager();
            um.on('update', this.onUpdate.createDelegate(this));
            return this.cPanel;            
        },

        onUpdate : function() {
            var div = Ext.DomQuery.selectNode('div#relation_tab', this.cPanel.getEl().dom);    
            if (div) {
                this.productSelector = new Mage.Catalog_Product_ProductSelect({
                    gridUrl : Mage.url + 'mage_catalog/product/gridData/category/1/'
                });
                this.initGrid({baseEl : Ext.get(div)});
            }
        },        
        
        initGrid : function(config) {
            if (!config.baseEl) {
                return false;
            }
            
            var baseEl = config.baseEl;

            var dataRecord = Ext.data.Record.create([
                {name: 'id', mapping: 'product_id'},
                {name: 'name', mapping: 'name'},
                {name: 'price', mapping: 'price'},
                {name: 'description', mapping: 'description'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'product_id'
            }, dataRecord);

             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.gridUrl}),
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
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
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
            this.buildGridToolbar();            
            this.loadGrid();
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
                text : 'Button 2',
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