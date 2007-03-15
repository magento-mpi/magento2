Mage.Catalog_Product = function(depend){
    var dep = depend;
    return {
        grid : null,
        ds : null,
        grid : null,
        searchPanel : null,
        
        init: function(){
            dep.init();
        },
        
        initGrid: function(catId, prnt) {
            
            var dataRecord = Ext.data.Record.create([
                {name: 'id', mapping: 'product_id'},
                {name: 'name', mapping: 'name'},
                {name: 'price', mapping: 'price'},
                {name: 'description', mapping: 'description'},
            ]);
                
            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'product_id'
            }, dataRecord);
                
             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: Mage.url + '/mage_catalog/product/gridData/category/' + catId + '/'}),
                reader: dataReader,
                remoteSort: true
             });
                
            dataStore.setDefaultSort('product_id', 'desc');
      

            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'id'},
                {header: "Name", sortable: true, dataIndex: 'name'},
                {header: "Price", sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
                {header: "Description", sortable: false, dataIndex: 'description'}
            ]);

            var grid = new Ext.grid.Grid(Ext.DomHelper.append(prnt, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                monitorWindowResize : true,
                autoHeight : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                enableColLock : false
            });
            
            grid.render();
            grid.getDataSource().load({params:{start:0, limit:25}});            
            
            var gridHead = grid.getView().getHeaderPanel(true);
            var gridFoot = grid.getView().getFooterPanel(true);
            
            var dataStore = grid.getDataSource();
            
            var paging = new Ext.PagingToolbar(gridHead, dataStore, {
                pageSize: 25,
                displayInfo: true,
                displayMsg: 'Displaying products {0} - {1} of {2}',
                emptyMsg: 'No products to display'                
            });
            
            paging.add('-', {
                text: 'Create New',
                cls: 'x-btn-text-icon product_new',
                handler : this.create,
                scope : this
            });
            
            paging.add({
                pressed: false,
                enableToggle: true,
                text: 'Search',
                handler : this.initSearch,
                scope : this,
                cls: 'x-btn-text-icon product_new'
            });
            
            this.grid = grid;
            return grid;
        },
        
        initSearch : function(btn, e) {
            var workZone = dep.getLayout('workZone');
            if (btn.pressed) {
                if (!this.searchPanel) {
                   workZone.beginUpdate();
                   this.searchPanel = new Ext.ContentPanel('', {autoCreate:true, closable: true, url: Mage.url + '/mage_catalog/category/new', loadOnce:true, title:'New Product'})
                   workZone.add('north', this.searchPanel);
                   workZone.endUpdate();
                } else {
                    workZone.getRegion('north').show();
                }
            } else {
                workZone.getRegion('north').hide();
            }
        },
        
        viewGrid : function (treeNode) {
            this.init();
            var workZone = dep.getLayout('workZone');            
            var grid = this.initGrid(treeNode.id, workZone.getEl());
            workZone.beginUpdate();
            workZone.add('center', new Ext.GridPanel(grid, {title: treeNode.text}));
            workZone.endUpdate();            
        },
        
        create: function() {
            this.init();
            var workZone = dep.getLayout('workZone');
            workZone.beginUpdate();
            workZone.add('south', new Ext.ContentPanel('', {autoCreate:true, closable: true, url: Mage.url + '/mage_catalog/category/new', loadOnce:true, title:'New Product'}));
            workZone.endUpdate();
        }
    }
}(Mage.Catalog);