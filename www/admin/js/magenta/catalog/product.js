/**
 * Product UI base layout
 */
Mage.Catalog_Product_Layout = function(container){
    var initialized = false;
    return {
        container : container,
        layout : null,
        panel : null,
        init : function(){
            container.init();
            if (!initialized){
                this.layout = new Ext.BorderLayout(container.getLayout('main').getEl().createChild({tag:'div'}), {
                    north : {
                        hideWhenEmpty : true,
                        titlebar:false,
                        split:true,
                        initialSize:27,
                        minSize:27,
                        maxSize:27,
                        autoScroll:true,
                        collapsible:true
                    },
                    center:{
                         titlebar: false,
                         autoScroll:true,
                         resizeTabs : true,
                         hideTabs : false,
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
                this.layout.getRegion('south').getEl().addClass('product-form-region');

                var centerLayout = new Ext.BorderLayout( Ext.DomHelper.append(container.getLayout('main').getEl(), {tag:'div'}, true), {
                    center:{
                        titlebar: true,
                        autoScroll:true,
                        resizeTabs : true,
                        hideTabs : true,
                        tabPosition: 'top'
                    }
                });
                centerLayout.getRegion('center').getEl().addClass('products-grid-region');

                centerLayout.beginUpdate();
                this.panel = centerLayout.add('center', new Ext.NestedLayoutPanel(this.layout, {title:''}));
                centerLayout.endUpdate();

                container.getLayout('main').add('center', new Ext.NestedLayoutPanel(centerLayout, {title:'Catalog Products'}));
                
                initialized = true;
            }
        }
    }
}(Mage.Catalog);

/**
 * Product grid
 */
Mage.Catalog_Product_Grid = function(container){
    var initialized = false;
    return {
        container : container,
        grid : null,
        toolbar : null,
        dataUrl : Mage.url + '/mage_catalog/product/gridData/',
        categoryId : null,
        pageSize : 30,

        init : function(){
            container.init();
            if (!initialized){
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

                var dataSource = new Ext.data.Store({
                    proxy: new Ext.data.HttpProxy({url: this.dataUrl}),
                    reader: dataReader,
                    remoteSort: true
                });
                
                dataSource.setDefaultSort('product_id', 'desc');

                var colModel = new Ext.grid.ColumnModel([
                    {header: "ID#", sortable: true, locked:false, dataIndex: 'id'},
                    {header: "Name", sortable: true, dataIndex: 'name'},
                    {header: "Price", sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
                    {header: "Description", sortable: false, dataIndex: 'description'}
                ]);
                
                this.grid = new Ext.grid.Grid(container.layout.getEl().createChild({tag: 'div'}), {
                    ds: dataSource,
                    cm: colModel,
                    autoSizeColumns : true,
                    loadMask: true,
                    monitorWindowResize : true,
                    autoHeight : true,
                    selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                    enableColLock : false
                });
                
                this.grid.on('rowclick', this._viewProduct.createDelegate(this));
                this.grid.render();

                this.toolbar = new Ext.PagingToolbar(this.grid.getView().getHeaderPanel(true), dataSource, {
                    pageSize: this.pageSize,
                    displayInfo: true,
                    displayMsg: 'Displaying products {0} - {1} of {2}',
                    emptyMsg: 'No products to display'
                });

                this.toolbar.insertButton(0, {
                    text: 'New Product',
                    cls: 'x-btn-text-icon btn_package_add',
                    handler : this._createProduct.createDelegate(this)
                 });
                
                this.toolbar.insertButton(1, new Ext.Toolbar.Separator());


                container.layout.beginUpdate();
                container.layout.add('center', new Ext.GridPanel(this.grid, {title:'test'}));
                container.layout.endUpdate();

                initialized = true;
            }
        },
        
        load : function(categoryId, categoryName){
            this.init();
            if (categoryName) {
                this.container.panel.setTitle(categoryName);
            }

            if (this.categoryId != categoryId) {
                this.categoryId = categoryId;
                this.grid.getDataSource().proxy.getConnection().url = this.dataUrl + 'category/' + categoryId + '/';
                this.grid.getDataSource().load({params:{start:0, limit:this.pageSize}});
            }
        },
        
        _viewProduct : function(){
        
        },
        
        _createProduct : function(){
        
        }
    }
}(Mage.Catalog_Product_Layout);

/**
 * Product data panel
 */
Mage.Catalog_Product_Panel = function(container){
    return {
        container : container,

        init : function(){
            container.init();
        },
        
        load : function(){
        },
        
        show : function(){
        },
        
        hide : function(){
        },
        
        clear: function(){
        },
        
        save : function(){
        }
    }
}(Mage.Catalog_Product_Layout);

/**
 * Product grid filter
 */
Mage.Catalog_Product_Filter = function(grid){
    return {
        grid : grid,

        init : function(){
        },
        load : function(){
        },
        show : function(){
        },
        hide : function(){
        },
        clear: function(){
        }
    }
}(Mage.Catalog_Product_Grid);

/**
 * Category data panel
 */
Mage.Catalog_Category_Panel = function(){
    return {
        init : function(){
        },
        load : function(){
        },
        show : function(){
        },
        hide : function(){
        },
        clear: function(){
        }
    }
}();