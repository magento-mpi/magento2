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
        ds : null,
        grid : null,
        searchPanel : null,
        editPanel : null,
        formLoading: null,
        loadedForms : new Ext.util.MixedCollection(),
        editablePanels : [],
        categoryEditFormPanel : null, // panel for category from
        productLyaout : null,
        productsGrid : null,
        registeredForms : new Ext.util.MixedCollection(),
        newItemDialog : null,
        
        
        init: function(){
            dep.init();
        },
        
        initLayouts : function() {
                var Layout = dep.getLayout('main');
                
                var Layout_Center = new Ext.BorderLayout( Ext.DomHelper.append(Layout.getEl(), {tag:'div'}, true), {
                     center:{
                         titlebar: true,
                         autoScroll:true,
                         resizeTabs : true,
                         hideTabs : true,
                         tabPosition: 'top'
                     },
                 });
                 
                 this.productLyaout = new Ext.BorderLayout(Layout.getEl().createChild({tag:'div'}), {
                    north: {
                        titlebar:true,
                        split:true,
                        initialSize:83,
                        minSize:0,
                        maxSize:200,
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
                     south: {
                         split:true,
                         initialSize:300,
                         minSize:50,
                         //maxSize:400,
                         titlebar: true,
                         autoScroll:true,
                         collapsible:true,
                         hideTabs : true
                      }
                 });
                
                this.productLyaout.beginUpdate();
                this.productLyaout.add('north',new Ext.ContentPanel(Ext.id(), {autoCreate: true, title:'Filter'},'north'));
                //this.productLyaout.add('center',new Ext.ContentPanel(Ext.id(), {autoCreate: true, title:'Grid'},'center'));
                this.productLyaout.add('south',new Ext.ContentPanel(Ext.id(), {autoCreate: true, title: 'Product Card'},'south'));
                this.productLyaout.endUpdate();
                
                Layout_Center.beginUpdate();
                Layout_Center.add('center', new Ext.NestedLayoutPanel(this.productLyaout, {title:'Cat Name'}));
                Layout_Center.endUpdate();
                
                Layout.add('center', new Ext.NestedLayoutPanel(Layout_Center, {title : 'Products Grid'}));                
        },
        
        initGrid: function(catId, prnt) {
            
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

            var grid = new Ext.grid.Grid(this.productLyaout.getEl().createChild({tag: 'div'}), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                monitorWindowResize : true,
                autoHeight : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                enableColLock : false
            });
            
            grid.on('rowclick', this.createItem.createDelegate(this));
            
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
            },{
                text: 'Add Filter',
                handler : this.addFilter,
                scope : this,
                cls: 'x-btn-text-icon'
            },{
                text: 'Apply Filters',
                handler : this.applyFilters,
                scope : this,
                cls: 'x-btn-text-icon'
            });
            
            this.grid = grid;
            return grid;
        },
        
        addFilter : function() {
            dep.getLayout('workZone').add('north', new Ext.ContentPanel('filters_panel', {autoCreate: true, title:'Filters', closable:true}));
            var workZoneCenterPanel = this.workZone.getRegion('north').getActivePanel();
            
            var filter = new Ext.Toolbar(Ext.DomHelper.insertFirst(workZoneCenterPanel.getEl(), {tag: 'div', id:'filter'+Ext.id()}, true));
            
            filter.add({
                text: 'Remove',
                handler : this.delFilter.createDelegate(filter, [this.grid]),
                cls: 'x-btn-text-icon'
            });
            
        	fieldSelect = Ext.DomHelper.append(workZoneCenterPanel.getEl(), {
		      tag:'select', children: [
    			{tag: 'option', value:'name', selected: 'true', html:'Name'},
	       		{tag: 'option', value:'size', html:'File Size'},
			    {tag: 'option', value:'lastmod', html:'Last Modified'}
              ]
        	}, true);

        	condSelect = Ext.DomHelper.append(workZoneCenterPanel.getEl(), {
		      tag:'select', children: [
    			{tag: 'option', value:'gt', selected: 'true', html:'Greater Than'},
	       		{tag: 'option', value:'eq', html:'Equal'},    			
    			{tag: 'option', value:'lt', html:'Lower Than'},
			    {tag: 'option', value:'like', html:'Like'}
              ]
        	}, true);
        	
        	textValue = Ext.DomHelper.append(workZoneCenterPanel.getEl(), {
		          tag:'input', type:'text', name:'filterValue'
		    }, true);
		    
            filter.add(fieldSelect.dom, condSelect.dom, textValue.dom);        	        	
        },
        
        applyFilters : function() {
        },
        
        delFilter : function(grid) {
            for(var i=0; i< this.items.length; i++) {
                if (this.items.get(i).destroy) {
                    this.items.get(i).destroy();
                }
            }
            this.el.removeAllListeners();
            this.el.remove();
            delete this.el;
            grid.getView().refresh();            
        },
       
        viewGrid : function (treeNode) {
            this.init();
            this.initLayouts();
            var grid = this.initGrid(treeNode.id);
//            this.productLayout.getLayout().setTitle(treeNode.text);
            this.productLayout.beginUpdate();
            this.productLayout.add('center',new Ext.GridPanel(grid, {title: treeNode.text}));
            this.productLayout.endUpdate();
        },
        
        
        setUpNewItem : function(menuItem, e) {
            if(!this.newItemDialog){ // lazy initialize the dialog and only create it once
                this.newItemDialog = new Ext.BasicDialog(Ext.DomHelper.append(document.body, {tag: 'div'}, true), { 
                        title : 'Test',
                        autoTabs:true,
                        width:300,
                        height:200,
                        modal:false,
                        shadow:true,
                        minWidth:300,
                        minHeight:200,
                        proxyDrag: true
                });
                var sbmt = this.newItemDialog.addButton('Ok', submit, this);
                sbmt.disable();
                this.newItemDialog.addButton('Cancel', this.newItemDialog.hide, this.newItemDialog);
                var mgr = new Ext.UpdateManager(this.newItemDialog.body);
                mgr.on('update', function(){sbmt.enable()});
                //this.newItemDialog.on('show', function(){mgr.update(Mage.url + '/mage_catalog/product/newoption/')})
                mgr.update(Mage.url + '/mage_catalog/product/newoption/');
            }
            this.newItemDialog.show(menuItem.getEl().dom);     
            
            var dialog = this.newItemDialog;
            
            function submit() {
                var set = Ext.DomQuery.selectNode('select#choose_attribute_set', dialog.body.dom);
                var type = Ext.DomQuery.selectNode('select#choose_product_type', dialog.body.dom);
                if (set) {
                    var setId = set.value;
                }
                if (type) {
                    var typeId = type.value;
                }
                this.newItemDialog.hide();
                this.doCreateItem.createDelegate(this, [-1, 'yes', setId, typeId], 0)();
            }
        },
        
        createItem: function() {
            var rowId = null;
            var menuItem = null;
            var e = null;
            
            switch (arguments.length) {
                case 2 :
                   menuItem = arguments[0];
                   e = arguments[1];
                   rowId = 0;
                   if (this.editablePanels.length) {
                        Ext.MessageBox.confirm('Product Card', 'You have unsaved product. Do you whant continue ?', this.setUpNewItem.createDelegate(this, [menuItem, e], 0));
                   } else {
                        this.setUpNewItem(menuItem, e);
                        return true;
                   }
                   break;
                case 3 :
                   rowId = arguments[1];
                   e = arguments[2];
                   if (this.editablePanels.length) {
                        Ext.MessageBox.confirm('Product Card', 'You have unsaved product. Do you whant continue ?', this.doCreateItem.createDelegate(this, [rowId], 0));
                   } else {
                        this.doCreateItem(rowId, 'yes');
                        return true;
                   }
                   break;
                default :
                    return false;
            };
        },
        
        
        doCreateItem: function(rowId, btn, setId, typeId) {
            var prodId = 0;
            setId = Number(setId);
            typeId = Number(typeId);
            if (btn == 'no') {
                return false;
            }
            var title = 'New Product'; // title for from layout            

            if (this.productLayout.getRegion('south').getActivePanel()) {
                this.productLayout.getRegion('south').clearPanels();
                this.editablePanels = [];
            }
            
            if (rowId >= 0) {
             try {
                  prodId = this.grid.getDataSource().getAt(rowId).id;
                  title = 'Edit: ' + this.grid.getDataSource().getById(prodId).get('name');
              } catch (e) {
                  Ext.MessageBox.alert('Error!', e.getMessage());
              }
            }
            this.editPanel = new Ext.BorderLayout(this.productLayout.getEl().createChild({tag:'div'}), {
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

            this.editPanel.add('north', new Ext.ContentPanel(this.productLayout.getEl().createChild({tag:'div'})));

            this.productLayout.beginUpdate();
            var failure = function(o) {Ext.MessageBox.alert('Product Card',o.statusText);}
            var cb = {
                success : this.loadTabs.createDelegate(this),
                failure : failure,
                argument : {prod_id: prodId}
            };
            var con = new Ext.lib.Ajax.request('GET', Mage.url + '/mage_catalog/product/card/product/'+prodId+'/setid/'+setId+'/typeid/'+typeId+'/', cb);  
            
            this.productLayout.add('south', new Ext.NestedLayoutPanel(this.editPanel, {closable: true, title:title}));
            this.productLayout.getRegion('south').on('panelremoved', this.onRemovePanel.createDelegate(this));
            this.productLayout.endUpdate();
        },

        onRemovePanel : function(region, panel) {
            this.editablePanels = [];
            this.registeredForms.clear();
        },

        // submit form to server        
        saveItem : function() {
            var region = this.editPanel.getRegion('center');
            var i = 0;
            var k = 0;
            var panel = null;
            var form = null;
            
            for(i = 0; i< region.panels.length; i++) {
                panel = region.panels.get(i);
                dq = Ext.DomQuery;
                form = null;
                
                if (panel.loaded) {
                   formEl = dq.selectNode('form', panel.getEl().dom);
                   if (form = this.registeredForms.get(formEl.action)) {
                       form.appendForm(formEl);
                   } else {
                       form = new Mage.Form(formEl);
                       this.registeredForms.add(form.action, form);
                   }
                }
            }
            
            form = null;
            for(i=0; i < this.registeredForms.getCount(); i++) {
                form = this.registeredForms.itemAt(i);
                form.sendForm(this.saveItemCallBack.createDelegate(this));
            }
        },
        
        saveItemCallBack : function(response, type) {
            Ext.dump(response.responseText);
        },
        
        
        loadTabs: function(response) {
            if (!this.editPanel) {
                return false;
            }            
            // decode responce from server - there is information about form tabs
            dataCard = Ext.decode(response.responseText);  
            
            // begin update editPanel
            this.editPanel.beginUpdate();
            
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
           
           // start draw panels and setup it to get infration from server
           var panel = null;
            for(var i=0; i < dataCard.tabs.length; i++) {
               var panel = this.createTabPanel(dataCard.tabs[i]);
               if (panel) {
                   var mgr = panel.getUpdateManager();
                   mgr.on('update', this.onLoadPanel.createDelegate(this, [panel], true));
                   this.editPanel.add('center', panel);
               }
            }
            
            for(var i=0; i < dataCard.tabs.length; i++) {
                if (dataCard.tabs[i].active) {
                    this.editPanel.getRegion('center').showPanel('productCard_' + dataCard.tabs[i].name);
                }
            }
            this.editPanel.endUpdate();
            // end update editPanel
            return true;
        },

        createTabPanel: function(tabInfo){
            var panel = null;
            if (tabInfo.type){
                // Relatet, bundle and super products panels
            }
            else{
                panel = new Ext.ContentPanel('productCard_' + tabInfo.name,{
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
        
        // set up form in panel - call after tab is updated by Ext.UpdateManager
        onLoadPanel : function(el, response) {
             var i=0;
            // we can ignore panel.loaded - because next step set it to ture version Ext alpha 3r4
            panel = this.editPanel.getRegion('center').getPanel(el.id);
            date = [];
            if (form = Ext.DomQuery.selectNode('form', panel.getEl().dom))  {
                var el;             
                for(i=0; i < form.elements.length; i++) {
                    // add to each file onChange event if - field changed - mark tab and form changed
                    Ext.EventManager.addListener(form.elements[i], 'change', this.onFormChange.createDelegate(this, [panel], true));
//                    el_type = form.elements[i].getAttribute('type');
//                    if (el_type && 'text' == el_type) {
//                         date[i] = new Ext.form.DateField({
//                               allowBlank:false
//                         });
//                        date[i].applyTo(form.elements[i].id);                   
//                    }
                }
                this.loadedForms.add(form.id, form);
            }
        },
       
        
        
        onFormChange : function(e, element, object, panel) {
            var i = 0;
            for(i=0; i<this.editablePanels.length; i++) {
                if (this.editablePanels[i] == panel.getId()) {
                    e.stopEvent();
                    return true;
                }
            }
            this.editablePanels.push(panel.getId());
            panel.setTitle(panel.getTitle() + '*');
            e.stopEvent();
        },
        
        loadCategoryEditForm : function(treeNode) {
            if (!this.categoryEditFormPanel) {
                var workZone = dep.getLayout('main');            
                workZone.beginUpdate();
                this.categoryEditFormPanel = workZone.add('center', new Ext.ContentPanel('', {autoCreate: true, url:Mage.url+'/mage_catalog/category/new', title: 'Edit: ' + treeNode.text, background:true}));
                workZone.endUpdate();           
            } else {
                this.categoryEditFormPanel.setTitle('Edit: ' + treeNode.text);
            }
        },
        
        
        
        cancelNew: function() {
            
        } 
    }
}(Mage.Catalog);


