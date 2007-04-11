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
        addressView : null,
        gridPanel:null,
        grid:null,
        addressLoading : null,
        addressViewUrl : Mage.url + '/mage_customer/address/gridData/', 
        addressViewForm : Mage.url + '/mage_customer/address/form/', 
        deleteAddressUrl : Mage.url + '/mage_customer/address/delete/', 

        customerCardUrl : Mage.url + '/mage_customer/customer/card/', 
        customerGridDataUrl : Mage.url + '/mage_customer/customer/gridData/',
        deleteUrl : Mage.url + '/mage_customer/customer/delete/', 
        formPanels : new Ext.util.MixedCollection(),
        forms2Panel : new Ext.util.MixedCollection(),
        forms : new Ext.util.MixedCollection(),
        customerCardId : null,

        
        formsEdit : [],
        
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
                this.grid.getDataSource().load({params:{start:0, limit:25}});
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
            
            //grid.on('click', this.showEditPanel.createDelegate(this));
            grid.on({
            	rowclick :  this.createItem.createDelegate(this),
            	rowcontextmenu : function(grid, rowIndex, e){
            	 	alert('context menu');
            	 	e.stopEvent();
            	}
            });            
            this.grid = grid;
           
            this.grid.render();
            
            var gridHead = this.grid.getView().getHeaderPanel(true);
            var gridFoot = this.grid.getView().getFooterPanel(true);
           
            var paging = new Ext.PagingToolbar(gridHead, this.grid.getDataSource(), {
                pageSize: 25,
                displayInfo: true,
                displayMsg: 'Displaying products {0} - {1} of {2}',
                emptyMsg: 'No products to display'                
            });
            
            paging.add('-', {
                text: 'Create New',
                cls: 'x-btn-text-icon product_new',
                handler : this.createItem.createDelegate(this, [this.customerLayout.getRegion('south')])
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

            return grid;
        },
        
        createItem: function(grid, rowIndex, e){
            try {
                if (typeof grid.getDataSource == 'function') {
                    this.customerCardId = grid.getDataSource().getAt(rowIndex).id;
                } else {
                    this.customerCardId = 0;
                }
            } catch (e) {
                alert(e);
                return false;
            }
            this.showEditPanel();
        },

        showEditPanel: function(){


            
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
            
            if (!this.customerCardId) {
                var deleteDisabled = true;
                var title = 'New Customer';
            } else {
                var deleteDisabled = false;
                // var title = this.grid.getDataSource().get(this.customerCardId).customer_firstname + ' ' + this.grid.getDataSource().get(this.customerCardId).customer_lastname;
            }
            // setup toolbar for forms
            toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(this.editPanel.getRegion('north').getEl().dom, {tag:'div'}, true));
            toolbar.add({
                text: 'Save',
                cls: 'x-btn-text-icon',
                handler : this.saveItem.createDelegate(this)
            },{
                text: 'Delete',
                cls: 'x-btn-text-icon',
                handler : this.onDelete.createDelegate(this),
                disabled : deleteDisabled
            },{
                text: 'Reset',
                cls: 'x-btn-text-icon',
                handler : this.onReset.createDelegate(this)
            },{
                text: 'Cancel',
                cls: 'x-btn-text-icon',
                handler : this.onCancelEdit.createDelegate(this)
            },'-');
            this.formLoading = toolbar.addButton({
               tooltip: 'Form is updating',
               cls: "x-btn-icon x-grid-loading",
               disabled: false
            });
            toolbar.addSeparator();
            
            this.editPanel.add('north', new Ext.ContentPanel(this.editPanel.getEl().createChild({tag:'div'}), {toolbar: toolbar}));
           
            var failure = function(o) {Ext.MessageBox.alert('Customer Card',o.statusText);}
            var cb = {
                success : this.loadTabs.createDelegate(this),
                failure : failure
            };
            

            var con = new Ext.lib.Ajax.request('GET', this.customerCardUrl + 'id/'+this.customerCardId+'/', cb);  

            this.customerLayout.add('south', new Ext.NestedLayoutPanel(this.editPanel, {closable: true, title:title}));
            this.customerLayout.getRegion('south').on('panelremoved', this.onRemovePanel.createDelegate(this));
        },
        

        
        loadTabs : function(response) {
            
            dataCard = Ext.decode(response.responseText);  
            
            
            // begin update editPanel
//            this.editPanel.beginUpdate();
            
            
            var panel = null;
            for(var i=0; i < dataCard.tabs.length; i++) {
               var panel = this.createTabPanel(dataCard.tabs[i]);
               if (panel) {
                   var mgr = panel.getUpdateManager();
                   mgr.on('update', this.onLoadPanel.createDelegate(this, [panel], true));
                   this.editPanel.add('center', panel);
                   dataCard.tabs[i]["panel"] = panel;
                   if (dataCard.tabs[i].type == 'form') {
                       this.formPanels.add(panel.getId(), panel);
                   }
               }
            }
            
            for (var i=0; i < dataCard.tabs.length; i++) {
               if (dataCard.tabs[i].active) {
                   this.editPanel.getRegion('center').showPanel(dataCard.tabs[i]["panel"]);
               }
            }
        },
        
        createTabPanel: function(tabInfo){
            var panel = null;
            switch (tabInfo.type) {
                case 'address' :
                    panel = this.createAddressTab(tabInfo);
                break;
                case 'form' :
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
        
        saveItem : function() {
            var i;
            for (i=0; i < this.formsEdit.length; i++) {
                if (this.forms.get(this.formsEdit[i])) {
                    var form = this.forms.get(this.formsEdit[i]);
                    form.sendForm(this.saveItemCallBack.createDelegate(this, [form.id], 0));
                    form.disable();
                    this.disableToolbar();
                    var panel = this.forms2Panel.get(form.id);
                }
            }    
        },
        
        saveItemCallBack : function(formId, response, type) {
            if (type.success) {
                alert('From POSTed');
                var panel = this.forms2Panel.get(formId);
                panel.setTitle(panel.getTitle().substr(0,panel.getTitle().length-1));
                this.formsEdit.splice(this.formsEdit.indexOf(formId),1);
            } else {
                Ext.dump(response.responseText);
            }
            this.enableToolbar();
            this.forms.get(formId).enable();
        },
        
        createAddressTab : function(tabInfo) {
            this.addressLayout = new Ext.BorderLayout(this.editPanel.getEl().createChild({tag:'div'}), {
                    hideOnLayout:true,
                    west: {
                        split:true,
                        initialSize: 300,
                        autoScroll:true,
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
            
            
            this.addressViewLayout = new Ext.BorderLayout(this.addressLayout.getRegion('west').getEl().createChild({tag:'div'}), {
                  hideOnLayout:true,
                  north : {
                      split:false,
                      initialSize: 27,
                      autoScroll:true,
                      titlebar:false,                        
                      collapsible:false
                  },
                  center : {
                      split:true,
                      initialSize: 300,
                      autoScroll:true,
                      titlebar:false,                        
                      collapsible:false
                  }
            });

//            this.addressViewLayout.beginUpdate();
            
            var toolbar = new Ext.Toolbar(this.addressViewLayout.getRegion('north').getEl().insertFirst({tag:'div'}, true));
            toolbar.add({
                text: 'New',
                cls: 'x-btn-text-icon',
                handler : this.onAddressNew.createDelegate(this)                
            },{
                text: 'Save',
                cls: 'x-btn-text-icon',
                handler : this.onAddressSave.createDelegate(this)
            },{
                text: 'Delete',
                cls: 'x-btn-text-icon',
                handler : this.onAddressDelete.createDelegate(this)
            },{
                text: 'Reset',
                cls: 'x-btn-text-icon',
                handler : this.onAddressReset.createDelegate(this)                
            },'-');
            this.addressLoading = toolbar.addButton({
               tooltip: 'Form is updating',
               cls: "x-btn-icon x-grid-loading",
               disabled: false
            });
            
            this.addressViewLayout.add('north', new Ext.ContentPanel(Ext.id(), {autoCreate: true, toolbar: toolbar}));
            var addressPanel = this.addressViewLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            this.addressLayout.endUpdate();                                    
            
            // setup toolbar for address
            
            this.addressLayout.add('west', new Ext.NestedLayoutPanel(this.addressViewLayout));
            this.addressLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true}, 'center'));
            
            var addressBody = addressPanel.getEl().createChild({tag:'div'});
            

        	// create the required templates
        	this.addressTemplate = new Ext.Template(
                '<div id="{addr_id}" class="address-view"><address>'+
            		'{address}<br/>'+
            		'{city}, {state} {zip}<br/>'+
            		'{country}'+
                '</address></div>'
        	);
        	this.addressTemplate.compile();	            
            
        	this.addressView = new Ext.JsonView(addressBody, this.addressTemplate, {
        		singleSelect: true,
        		jsonRoot: 'addresses',
        		emptyText : '<div style="padding:10px;">No address found</div>'
        	});     
        	
        	this.addressView.on({
        	    'load' : function () {
        	        this.addressView.select(0);
        	        this.onClickAddressView(this.addressView, 0, this.addressView.getSelectedNodes()[0]);
        	    },
            	'loadexception' : function (view, data, response) {
            	    alert('loadExceptionEvent');
            	},
            	'click' : this.onClickAddressView.createDelegate(this),
            	scope : this
        	});
        	
            var panel = new Ext.NestedLayoutPanel(this.addressLayout, { closable : false, background: !tabInfo.active, title: 'Addresses'});
            panel.on('activate', function() {
                this.addressView.load({url:this.addressViewUrl + 'id/'+ this.customerCardId +'/', scripts:false});
            }, this);

            return panel;
        },
        
        disableToolbar : function() {
            var toolbar = this.editPanel.getRegion('north').getActivePanel().getToolbar();
            for(var i=0; i< toolbar.items.length; i++) {
                if (toolbar.items.get(i).disable) {
                    toolbar.items.get(i).disable();
                }
            }            
        },
        
        enableToolbar : function() {
            var toolbar = this.editPanel.getRegion('north').getActivePanel().getToolbar();
            for(var i=0; i< toolbar.items.length; i++) {
                if (toolbar.items.get(i).enable) {
                    toolbar.items.get(i).enable();
                }
            }            
        },

        disableAddressToolbar : function() {
            var toolbar = this.addressViewLayout.getRegion('north').getActivePanel().getToolbar();
            for(var i=0; i< toolbar.items.length; i++) {
                if (toolbar.items.get(i).disable) {
                    toolbar.items.get(i).disable();
                }
            }            
        },
        
        enableAddressToolbar : function() {
            var toolbar = this.addressViewLayout.getRegion('north').getActivePanel().getToolbar();
            for(var i=0; i< toolbar.items.length; i++) {
                if (toolbar.items.get(i).enable) {
                    toolbar.items.get(i).enable();
                }
            }            
        },

        
        onClickAddressView : function(view, index, node, e) {
            if (view.jsonData.length > 0 ) {
                this.addressView.select(index);
                var panel = this.addressLayout.getRegion('center').getActivePanel();
                panel.setUrl(this.addressViewForm + 'id/' + node.id);
                panel.refresh();
            }            
        },
        
    	onLoadException : function(v,o){
	       this.view.getEl().update('<div style="padding:10px;">Error loading images.</div>'); 
    	}, 
        
        onRemovePanel: function(region, panel) {
            region.clearPanels();
        },
        
        onCancelEdit : function () {
            this.customerLayout.getRegion('south').clearPanels();            
        },
        
        onDelete : function() {
            this.disableToolbar();
            var cb = {
                success : this.onDeleteSuccess.createDelegate(this),
                failure : this.onDeleteFailure.createDelegate(this)
            }
            var con = new Ext.lib.Ajax.request('GET', this.deleteUrl + 'id/' + this.customerCardId + '/', cb);
        },
        
        onDeleteSuccess : function() {
            this.onCancelEdit();
            if (this.gridPanel) {
                this.gridPanel.getGrid().getDataSource().reload();
            }
        },
        
        onDeleteFailure : function() {
            alert('Customer delete failure');
            this.enableToolbar();
        },
        
        
        onReset : function() {
            var panel = this.editPanel.getRegion('center').getActivePanel();
            var formEl = Ext.DomQuery.selectNode('form', panel.getEl().dom);
            if (formEl && this.formsEdit.indexOf(formEl.id) >= 0) {
                formEl.reset();
                this.formsEdit.splice(this.formsEdit.indexOf(formEl.id),1);            
                panel.setTitle(panel.getTitle().substr(0,panel.getTitle().length-1));            
            }
        },
        
        onLoadPanel : function(el, response) {
            if (!this.formPanels.get(el.id)) {
                return false;
            }
             var i=0;
            // we can ignore panel.loaded - because next step set it to ture version Ext alpha 3r4
            panel = this.formPanels.get(el.id);
            var form = null;
            if (form = Ext.DomQuery.selectNode('form', panel.getEl().dom))  {
                var el;             
                if (!form.id) {
                    form.id = Ext.id();
                }
                
                for(i=0; i < form.elements.length; i++) {
                    // add to each file onChange event if - field changed - mark tab and form changed
                    Ext.EventManager.addListener(form.elements[i], 'change', this.onFormChange.createDelegate(this, [panel, form.id], 0));
                }
                this.forms.add(form.id, new Mage.Form(form));
                this.forms2Panel.add(form.id, panel);
            }
        },
        
        onFormChange : function(panel, formId, e, element, object) {
            var i;
            for (i = 0; i < this.formsEdit.length; i++) {
                if (this.formsEdit[i] == formId) {
                    return false;
                }
            }
            if (this.forms.get(formId)) {
                panel.setTitle(panel.getTitle() + '*');
                this.formsEdit.push(formId);
            }
            e.stopEvent();
        },
        
        onAddressNew : function() {
              var panel = this.addressLayout.getRegion('center').getActivePanel();
              panel.setUrl(this.addressViewForm + 'id/0/');
              panel.refresh();
        },

        onAddressSave : function() {
            this.disableAddressToolbar();
            var panel = this.addressLayout.getRegion('center').getActivePanel();
            var formEl = Ext.DomQuery.selectNode('form', panel.getEl().dom);
            var form = new Mage.Form(formEl);
            form.sendForm(this.onAddressSaveCallback.createDelegate(this));
        },
        
        onAddressSaveCallback : function(response, type) {
             if (response.status == 200) {
                 alert('OK');
             }
            this.enableAddressToolbar();             
        },

        onAddressDelete : function () {
            this.disableAddressToolbar();
            var selNode = this.addressView.getSelectedNodes()[0]
            if (selNode) {
                var addressId = selNode.id;                
                var cb = {
                  success : this.onAddressDeleteSuccess.createDelegate(this),
                  failure : this.onAddressDeleteFailure.createDelegate(this),
                  argument : {"addressId": this.addressView.getSelectedIndexes()[0]}
                }
                var con = new Ext.lib.Ajax.request('GET', this.deleteAddressUrl + 'id/' + addressId + '/', cb);
            } else {
                this.enableAddressToolbar();
            }
        },
        
        onAddressDeleteSuccess : function(response) {
            var addressIndex = response.argument.addressId;
            this.addressView.jsonData.splice(addressIndex,1);
            this.addressView.refresh();                        
            if (this.addressView.jsonData.length > 0) {            
                this.addressView.select(0)
                this.onClickAddressView(this.addressView, 0, this.addressView.getSelectedNodes()[0]);
            } else {
                var panel = this.addressLayout.getRegion('center').getActivePanel();
                panel.setContent('');
            }
            this.enableAddressToolbar();
        },
        
        onAddressDeleteFailure : function() {
            alert('Delete Failure');
            this.enableAddressToolbar();
        },
        
        onAddressReset : function() {
            var panel = this.addressLayout.getRegion('center').getActivePanel();
            var formEl = Ext.DomQuery.selectNode('form', panel.getEl().dom);
            formEl.reset();
        }
    }
}();
