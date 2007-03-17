//create renderer for new product
Mage.Catalog_Product_Renderer = function(){};

Mage.Catalog_Product_Renderer.prototype = {
    render :  function(el, response, updateManager, callback) {
        el.dom.innerHTML = '';
    }
}

Mage.Catalog_Product = function(depend){
    var dep = depend;
    return {
        grid : null,
        ds : null,
        grid : null,
        searchPanel : null,
        editPanel : null,
        
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
        
        create: function(newItem) {
            if (!this.grid) {
                return false;
            }
            
            var workZone = dep.getLayout('workZone');
            if (workZone.getRegion('south').getActivePanel()) {
                return false;
            }
            
            newItem = true;
            
            var Layout_south = new Ext.BorderLayout(Ext.DomHelper.append(workZone.getEl(), {tag:'div'}, true), {
                    hideOnLayout:true,
                    north: {
                        split:true,
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

            Layout_south.add('north', new Ext.ContentPanel(Ext.DomHelper.append(workZone.getEl(), {tag:'div'}, true),{}));
            Layout_south.add('center', new Ext.ContentPanel(Ext.DomHelper.append(workZone.getEl(), {tag:'div'}, true),{}));
            
            var toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(Layout_south.getRegion('north').getEl().dom, {tag:'div'}, true));
            toolbar.add({
                text: 'Save',
                cls: 'x-btn-text-icon'
            },{
                text: 'Delete',
                cls: 'x-btn-text-icon'
            },{
                text: 'Reset',
                cls: 'x-btn-text-icon'
            },{
                text: 'Cancel',
                cls: 'x-btn-text-icon'
            });
            
            workZone.beginUpdate();
            
            
//       var dataCard = Ext.decode(response.responseText);        
//        
//       var proCard_Form = Ext.DomHelper.append(el, {tag: 'form', action:dataCard.form.action, method:dataCard.form.method}, true);
//       
//       var tabContainer = Ext.DomHelper.append(proCard_Form, {tag:'div'}, true);
//       var formTabs = new Ext.TabPanel(tabContainer);
//       
//       for(var i=0; i < dataCard.tabs.length; i++) {
//            var tab = formTabs.addTab('productCard_' + dataCard.tabs[i].name, dataCard.tabs[i].title);
//            if (dataCard.tabs[i].url) {
//                var updater = tab.getUpdateManager();
//                updater.setDefaultUrl(dataCard.tabs[i].url);
//                tab.on('activate', updater.refresh, updater, true);
//            }
//            if (dataCard.tabs[i].isActive) {
//                tab.activate();
//            }
//            if (dataCard.tabs[i].isDisabled) {
//                tab.disable();
//            }
//       }

            workZone.add('south', new Ext.NestedLayoutPanel(Layout_south, {title:'New Product(add combobox for attr set if total recods >1, pls. Onchange-send set id and reload panel)'}));
            workZone.endUpdate();
        },
        
        cancelNew: function() {
            
        } 
        
        
    }
}(Mage.Catalog);


