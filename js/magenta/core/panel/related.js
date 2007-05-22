Mage.core.PanelRelated = function(region, config) {
    this.region = region;
    this.notLoaded = false;
    Ext.apply(this, config);
    
    this.gridUrl = Mage.url + 'product/relatedList/';
    
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
    
    
    var dataStore = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({url: this.gridUrl + 'product/' + this.record.data.id + '/'}),
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

    this.grid = new Ext.grid.Grid(this.region.getEl().createChild({tag : 'div'}), {
        ds: dataStore,
        cm: colModel,
        autoSizeColumns : true,
        loadMask: true,
        monitorWindowResize : true,
        autoHeight : false,
        selModel : new Ext.grid.RowSelectionModel({singleSelect : false}),
        enableColLock : false
    });
    
    this.panel = this.region.add(new Ext.GridPanel(this.grid, {
        background : true,
        fitToFrame : true,
        title : config.title || 'Title'
    }));
    
    this.panel.on('activate', function(){
        this.grid.render();
        this.grid.autoSize();
        this.grid.getDataSource().load();
        this.notLoaded = false;
    }, this, {single : true});

    this.panel.on('activate', function(){
        if (this.notLoaded) {
            this.grid.getDataSource().proxy.getConnection().url = this.gridUrl + 'product/' + this.record.data.id + '/';
            this.grid.getDataSource().load();
            this.notLoaded = false;        
        }
    }, this);
    
};

Ext.extend(Mage.core.PanelRelated, Mage.core.Panel, {
    update : function(config) {
        Ext.apply(this, config);
        if (this.region.getActivePanel() === this.panel) {
            this.grid.getDataSource().proxy.getConnection().url = this.gridUrl + 'product/' + this.record.data.id + '/';        
            this.grid.getDataSource().load();
        } else {
            this.notLoaded = true;
        }
    }  
})
