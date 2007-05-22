Mage.Customer = function(depend){
    var loaded = false;
    var Layout = null;
    var gridLayout = null;
    return {
        _layouts : new Ext.util.MixedCollection(true),
        baseLayout:null,
        customerLayout: null,
        editLayout : null,
        addressLayout : null,
        addressView : null,
        gridPanel:null,
        grid:null,
        addressLoading : null,
        addressPanel : null,
        addressViewUrl : Mage.url + 'address/gridData/',
        addressViewForm : Mage.url + 'address/form/',
        deleteAddressUrl : Mage.url + 'address/delete/',

        customerCardUrl : Mage.url + 'customer/card/',
        customerGridDataUrl : Mage.url + 'customer/gridData/',
        deleteUrl : Mage.url + 'customer/delete/',
        formPanels : new Ext.util.MixedCollection(),
        forms2Panel : new Ext.util.MixedCollection(),
        forms : new Ext.util.MixedCollection(),
        customerCardId : null,
        lastSelectedCustomer : null,
        
        tabCollections : new Ext.util.MixedCollection(),

        formsEdit : [],

        init : function() {
            var Core_Layout = Mage.Admin.getLayout();
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
                        hideTabs:true,
                        preservPanels : true
                    },
                    south : {
                        preservePanels : true,
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
                
                this.customerCard = new Mage.core.ItemCard({
                    region : this.customerLayout.getRegion('south'),
                    url : Mage.url + 'customer/card/id/'
                });

            } else { // not loaded condition
                Mage.Admin.getLayout().getRegion('center').showPanel(this.baseLayout);
            }
        },

        viewGrid: function(){
            this.init();
            if (!this.gridPanel){
                var grid = this.initGrid();
                this.customerLayout.beginUpdate();
                this.gridPanel = this.customerLayout.add('center',new Ext.GridPanel(grid));
                this.customerLayout.endUpdate();
                this.grid.getDataSource().load({params:{start:0, limit:25}});
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
            
            grid.getSelectionModel().on('rowselect', this.loadCustomer.createDelegate(this));

            this.grid = grid;

            this.grid.render();

            var gridHead = this.grid.getView().getHeaderPanel(true);
            var gridFoot = this.grid.getView().getFooterPanel(true);

            var paging = new Ext.PagingToolbar(gridHead, this.grid.getDataSource(), {
                pageSize: 25,
                displayInfo: true,
                displayMsg: 'Displaying customers {0} - {1} of {2}',
                emptyMsg: 'No customers to display'
            });

            paging.add('-', {
                text: 'Create New',
                cls: 'x-btn-text-icon btn-add-user',
                handler : this.createItem.createDelegate(this, [this.customerLayout.getRegion('south')])
            });
            return grid;
        },
        

        loadCustomer : function(sm, rowIndex, record) {
            this.customerCard.loadRecord(record);
        },
        
        
        createItem: function(sm, rowIndex, record){
            if (record) {
                if (this.customerCardId == record.id) {
                    return false;
                } else {
                    this.customerCardId = record.id;                        
                }
            } else {
                this.customerCardId = 0;
            }
            this.showEditPanel();
        },

        showEditPanel: function(){
            if (!this.editPanel) {
                this.editLayout = new Ext.BorderLayout(this.customerLayout.getEl().createChild({tag:'div'}), {
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
                         preservePanels : true,
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
                    var title = 'Edit Customer #'+this.customerCardId;
                }
            
                // setup toolbar for forms
                toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(this.editLayout.getRegion('north').getEl().dom, {tag:'div'}, true));
                toolbar.add({
                    text: 'Save',
                    cls: 'x-btn-text-icon btn-accept',
                    handler : this.saveItem.createDelegate(this)
                },{
                    text: 'Delete',
                    cls: 'x-btn-text-icon btn-bin-closed',
                    handler : this.onDelete.createDelegate(this),
                    disabled : deleteDisabled
                },{
                    text: 'Reset',
                    cls: 'x-btn-text-icon btn-arrow-undo',
                    handler : this.onReset.createDelegate(this)
                },{
                    text: 'Cancel',
                    cls: 'x-btn-text-icon btn-cancel',
                    handler : this.onCancelEdit.createDelegate(this)
                },'-');
                this.formLoading = toolbar.addButton({
                   tooltip: 'Form is updating',
                   cls: "x-btn-icon x-grid-loading",
                   disabled: false
                });
                toolbar.addSeparator();

                this.editLayout.add('north', new Ext.ContentPanel(this.editLayout.getEl().createChild({tag:'div'}), {toolbar: toolbar}));
                this.editPanel = this.customerLayout.add('south', new Ext.NestedLayoutPanel(this.editLayout, {closable: true, title:title}));
            } else {
                 if (!this.customerCardId) {
                    var title = 'New Customer';
                 } else {
                    var title = 'Edit Customer #'+this.customerCardId;
                 }
                 this.editPanel.setTitle(title);
                 this.customerLayout.add('south', this.editPanel);
            }
            

                var conn = new Ext.data.Connection();
                
                conn.on('requestcomplete', this.loadTabs.createDelegate(this));                
                
                conn.on('requestexception', function(){
                    Ext.MessageBox.alert('Error','Critical Error!!!');                
                })

                conn.request({
                    method : 'GET',
                    url : this.customerCardUrl + 'id/'+this.customerCardId+'/'
                });
        },

        loadTabs : function(conn, response, options) {
            var dataCard  = null;
            dataCard = Ext.decode(response.responseText);

            var panel = null;
            for(var i=0; i < dataCard.tabs.length; i++) {
               var panel = this.createTabPanel(dataCard.tabs[i]);
               if (panel) {
                   this.editLayout.add('center', panel);
                   if (dataCard.tabs[i].type == 'form') {
                       this.formPanels.add(panel.getId(), panel);
                   }
               }
            }
            this.editLayout.getRegion('center').showPanel(this.formPanel);
        },

        createTabPanel: function(tabInfo){
            var panel = null;
            switch (tabInfo.type) {
                case 'address' :
                    panel = this.createAddressTab(tabInfo);
                break;
                case 'form' :
                    panel = this.careateFormTab(tabInfo);
                    break;
                default :
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
                var panel = this.forms2Panel.get(formId);
                panel.setTitle(panel.getTitle().substr(0,panel.getTitle().length-1));
                this.formsEdit.splice(this.formsEdit.indexOf(formId),1);
            } else {
                Ext.dump(response.responseText);
            }
            this.enableToolbar();
            this.forms.get(formId).enable();
        },
        
        
        careateFormTab : function(tabInfo) {
            if (!this.formPanel) {
                this.formPanel = new Ext.ContentPanel('customerCard_' + tabInfo.name,{
                    title : tabInfo.title,
                    autoCreate: true,
                    loadOnce : true,
                    closable : false
                });
                this.formPanel.load(tabInfo.url);                
                var mgr = this.formPanel.getUpdateManager();
                mgr.on('update', this.onLoadPanel.createDelegate(this, [this.formPanel], true));
            } else {
                this.formPanel.setTitle(tabInfo.title);
                this.formPanel.load(tabInfo.url);
            }
            return this.formPanel;
        },

        createAddressTab : function(tabInfo) {
            this.addressesLoaded = false;
            if (!this.addressPanel) {

                this.addressLayout = new Ext.BorderLayout(this.editLayout.getEl().createChild({tag:'div'}), {
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
                
                var toolbarCPEl = this.addressViewLayout.getRegion('north').getEl().createChild({tag:'div'});
                
                var toolbar = new Ext.Toolbar(toolbarCPEl.createChild({tag : 'div'}));
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
                   disabled: false,
                   handler : function() {
                       if (this.addressView) {
                        this.addressView.load({url:this.addressViewUrl + 'id/'+ this.customerCardId +'/', scripts:false});
                       }
                   },
                   scope : this
                });
            
                var addressPanelInnerToolbar = this.addressViewLayout.add('north', new Ext.ContentPanel(toolbarCPEl, {toolbar: toolbar}));
                
                var addressPanelInner = this.addressViewLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate : true}));


                this.addressLayout.add('west', new Ext.NestedLayoutPanel(this.addressViewLayout));
                
                this.addressLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true, loadOnce : true}));
                
                
                var addressBody = addressPanelInner.getEl().createChild({tag:'div'});


                // create the required templates
                this.addressTemplate = new Ext.Template(
                    '<div id="{address_id}" class="address-view"><address>{address}</address></div>'
                );
                this.addressTemplate.compile();

                this.addressView = new Ext.JsonView(addressBody, this.addressTemplate, {
                    singleSelect: true,
                    jsonRoot: 'addresses',
                    emptyText : '<div class="address-view"><h3>No address found</h3></div>'
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
            
                
                this.addressPanel = new Ext.NestedLayoutPanel(this.addressLayout, { closable : false, background: !tabInfo.active, title: 'Addresses'});
                this.addressPanel.on('activate', function() {
                        if (this.addressesLoaded == false ) {
                            this.addressView.load({url:this.addressViewUrl + 'id/'+ this.customerCardId +'/', scripts:false});
                            this.addressesLoaded  = true;
                        }
                }.createDelegate(this));
            }
            return this.addressPanel;
        },

        disableToolbar : function() {
            var toolbar = this.editLayout.getRegion('north').getActivePanel().getToolbar();
            for(var i=0; i< toolbar.items.length; i++) {
                if (toolbar.items.get(i).disable) {
                    toolbar.items.get(i).disable();
                }
            }
        },

        enableToolbar : function() {
            var toolbar = this.editLayout.getRegion('north').getActivePanel().getToolbar();
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
                panel.setUrl(this.addressViewForm + 'id/' + node.id + '/customer/' + this.customerCardId);
                panel.refresh();
            }
            if (this.customerCardId == 0) {
                this.addressPanel.getLayout().getRegion('center').getActivePanel().setContent('');
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
            var panel = this.editLayout.getRegion('center').getActivePanel();
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
              panel.setUrl(this.addressViewForm + 'id/0/customer/' + this.customerCardId);
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
            this.addressView.refresh();
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
