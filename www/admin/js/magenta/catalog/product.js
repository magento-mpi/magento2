Mage.Catalog_Product = function(depend){
    var dep = depend;
    return {
        grid : null,
        ds : null,
        
        init: function(){
            dep.init();
        },
        
        initGrid: function(prnt) {
            
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
                    proxy: new Ext.data.HttpProxy({url: Mage.url + '/mage_catalog/product/gridData/category/1/'}),
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
                    autoSizeColumns: true,
                    monitorWindowResize: true,
                    autoHeight:true,
                    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
                    enableColLock:false
                });

                grid.render();
                var gridHead = grid.getView().getHeaderPanel(true);
                var gridFoot = grid.getView().getFooterPanel(true);
                var paging = new Ext.PagingToolbar(gridFoot, dataStore, {pageSize: 20});

                var displayInfo = gridHead.createChild({cls:'paging-info'});
                dataStore.on('load', function(){
                    var count = dataStore.getCount();
                    var msg = count == 0 ?
                        "No products to display" :
                        String.format('Displaying products {0} - {1} of {2}', paging.cursor+1, paging.cursor+count, dataStore.getTotalCount());
                    displayInfo.update(msg);
            });

            dataStore.load({params:{start:0, limit:20}});
            return grid;
        },
        
        viewGrid : function () {
            this.init();
            var workZone = dep.getLayout('workZone');            
            var grid = this.initGrid(workZone.getEl());
            workZone.add('center', new Ext.GridPanel(grid, {title:'Products'}));
        },
        
        create: function() {
            this.init();
            var workZone = dep.getLayout('workZone');
            workZone.add('south', new Ext.ContentPanel('', {autoCreate:true, url: Mage.url + '/mage_catalog/category/new', loadOnce:true, title:'New Product'}, 'Form will be there'));
        }
    }
}(Mage.Catalog);