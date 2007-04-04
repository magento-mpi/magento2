Mage.Customer = function(depend){
    var loaded = false;
    var Layout = null;
    var gridLayout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        baseLayout:null,
        customerLayout: null,
        editPanel : null,
        addressLayout : null,
        gridPanel:null,
        grid:null,
        
        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!this.baseLayout) {
                this.baseLayout = new Ext.BorderLayout( Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                     center:{
                         titlebar: false,
                         autoScroll: true,
                         resizeTabs : true,
                         hideTabs : true,
                         tabPosition: 'top'
                     },
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
                        hideTabs:true
                    },
                    south : {
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
                
            } else { // not loaded condition
                Mage.Core.getLayout().getRegion('center').showPanel(this.baseLayout);
            }
        },

        viewGrid: function(){
            this.init();
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
            this.viewGrid();
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
            
            //grid.on('click', this.showEditPanel.createDelegate(this));
            grid.on({
            	rowclick :  this.createItem.createDelegate(this),
            	rowcontextmenu : function(grid, rowIndex, e){
            	 	alert('context menu');
            	 	e.stopEvent();
            	},
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
                handler : this.createItem.createDelegate(this)
            });
//            ,{
//                text: 'Add Filter',
//                scope : this,
//                cls: 'x-btn-text-icon'
//            },{
//                text: 'Apply Filters',
//                scope : this,
//                cls: 'x-btn-text-icon'
//            });
            
            this.grid = grid;

            return grid;
        },
        
        createItem: function(){
            this.showEditPanel();
        },

        showEditPanel: function(){
            var title = 'New Customer';

            
            if (this.customerLayout.getRegion('south') && this.customerLayout.getRegion('south').getActivePanel()) {
                this.customerLayout.getRegion('south').clearPanels();
                this.customerLayout.getRegion('south').hide();
            }
            
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
            this.editPanel.add('north', new Ext.ContentPanel(this.editPanel.getEl().createChild({tag:'div'})));
           
            var failure = function(o) {Ext.MessageBox.alert('Customer Card',o.statusText);}
            var cb = {
                success : this.loadTabs.createDelegate(this),
                failure : failure
            };
            var con = new Ext.lib.Ajax.request('GET', Mage.url + '/mage_customer/customer/card/customer/0/', cb);  

            this.customerLayout.add('south', new Ext.NestedLayoutPanel(this.editPanel, {closable: true, title:title}));
            this.customerLayout.getRegion('south').on('panelremoved', this.onRemovePanel.createDelegate(this));
        },
        
        onRemovePanel: function(region, panel) {
            //region.hide();
            region.clearPanels();
        },
        
        onLoadPanel : function() {
            
        },
        
        loadTabs : function(response) {
            
            dataCard = Ext.decode(response.responseText);  
            
            
            // begin update editPanel
//            this.editPanel.beginUpdate();
            
            // setup toolbar for forms
            var toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(this.editPanel.getRegion('north').getEl().dom, {tag:'div'}, true));
            toolbar.add({
                text: 'Save',
                cls: 'x-btn-text-icon',
                handler : this.saveItem.createDelegate(this)
            },{
                text: 'Delete',
                cls: 'x-btn-text-icon'
            },{
                text: 'Reset',
                cls: 'x-btn-text-icon'
            },{
                text: 'Cancel',
                cls: 'x-btn-text-icon'
            },'-');
            this.formLoading = toolbar.addButton({
               tooltip: 'Form is updating',
               cls: "x-btn-icon x-grid-loading",
               disabled: false
            });
            toolbar.addSeparator();
            
            
            dataCard.tabs[0].type = 'address';
            var panel = null;
            for(var i=0; i < dataCard.tabs.length; i++) {
               var panel = this.createTabPanel(dataCard.tabs[i]);
               if (panel) {
                   var mgr = panel.getUpdateManager();
                   mgr.on('update', this.onLoadPanel.createDelegate(this, [panel], true));
                   this.editPanel.add('center', panel);
               }
            }
            //this.editPanel.endUpdate();
            
        },
        
        createTabPanel: function(tabInfo){
            var panel = null;
            switch (tabInfo.type) {
                case 'address' :
                    panel = this.createAddressTab(tabInfo);
                break;
                default : 
                    panel = new Ext.ContentPanel('customerCard_' + tabInfo.name,{
                        title : tabInfo.title,
                        autoCreate: true,
                        closable : false,
                        url: tabInfo.url,
                        loadOnce: true,
                        background: true
                    });
            }
            return panel;
        },
        
        createAddressTab : function(tabInfo) {
            this.addressLayout = new Ext.BorderLayout(this.editPanel.getEl().createChild({tag:'div'}), {
                    hideOnLayout:true,
                    west: {
                        split:true,
                        initialSize: 200,
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
            
            this.addressLayout.beginUpdate();
            var addressPanel = this.addressLayout.add('west', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            this.addressLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true},'center'));
            this.addressLayout.endUpdate();            
            
            var addressBody = addressPanel.getEl().createChild({tag:'div', cls:'ychooser-view'});
            
        	// create the required templates
        	this.addressTemplate = new Ext.Template(
                '<address id="{addr_id}">'+
            		'{address}<br/>'+
            		'{city}, {state} {zip}<br/>'+
            		'{country}'+
                '</address>'
        	);
        	this.addressTemplate.compile();	            
            
        	var view = new Ext.JsonView(addressBody, this.addressTemplate, {
        		singleSelect: true,
        		jsonRoot: 'addresses',
        		emptyText : '<div style="padding:10px;">No address found</div>'
        	});            
            
            view.on("click", function(vw, index, node, e){
                 alert('Node "' + node.id + '" at index: ' + index + " was clicked.");
             });

            view.load({url: Mage.url + '/mage_customer/address/gridData/customer/14/'});
            
            var panel = new Ext.NestedLayoutPanel(this.addressLayout, {title: 'Addresses'});
            return panel;
        },
        
        saveItem : function() {
            
        }
        
    }
}();
