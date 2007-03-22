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
        formLoading: null,
        loadedForms : new Ext.util.MixedCollection(),
        editablePanels : [],
        registeredForms : new Ext.util.MixedCollection(),
        newItemDialog : null,
        
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
            
            grid.on('rowdblclick', this.createItem.createDelegate(this));
            
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
                handler : this.setUpNewItem.createDelegate(this)
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
            var workZoneCenterPanel = dep.getLayout('workZone').getRegion('north').getActivePanel();
            
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
            var workZone = dep.getLayout('workZone');            
            var grid = this.initGrid(treeNode.id, workZone.getEl());
            workZone.beginUpdate();
            workZone.add('center', new Ext.GridPanel(grid, {title: treeNode.text}));
            workZone.endUpdate();            
        },
        
        
        setUpNewItem : function(menuItem, e) {
            if(!this.newItemDialog){ // lazy initialize the dialog and only create it once
                this.newItemDialog = new Ext.BasicDialog(Ext.DomHelper.append(document.body, {tag: 'div'}, true), { 
                        autoTabs:true,
                        width:200,
                        height:100,
                        modal:true,
                        shadow:true,
                        minWidth:300,
                        minHeight:250,
                        proxyDrag: true
                });
                this.newItemDialog.addButton('Submit', this.newItemDialog.hide, this.newItemDialog).disable();
                this.newItemDialog.addButton('Close', this.newItemDialog.hide, this.newItemDialog);
            }
            this.newItemDialog.show(menuItem.getEl().dom);     
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
                   break;
                case 3 :
                   rowId = arguments[1];
                   e = arguments[2];
                   break;
                default :
                    return false;
            };
            
            // check if we have editable panels
            if (this.editablePanels.length) {
                Ext.MessageBox.confirm('Product Card', 'You have unsaved product. Do you whant continue ?', this.doCreateItem.createDelegate(this, [rowId], 0));
            } else {
                this.doCreateItem(rowId, 'yes');
                return true;
            }
                
            // check if we have opened panels

        },
        
        
        doCreateItem: function(rowId, btn) {
            var prodId = 0;
            if (btn == 'no') {
                return false;
            }
            var title = 'New Product'; // title for from layout            
            var setId = arguments[2] || 0;

            var workZone = dep.getLayout('workZone');
            if (workZone.getRegion('south').getActivePanel()) {
                workZone.getRegion('south').clearPanels();
                this.editablePanels = [];
            }
            
            if (rowId != 0) {
             try {
                  prodId = this.grid.getDataSource().getAt(rowId).id;
                  title = 'Edit: ' + this.grid.getDataSource().getById(prodId).get('name');
              } catch (e) {
                  Ext.MessageBox.alert('Error!', e.getMessage());
              }
            };
            
            this.editPanel = new Ext.BorderLayout(Ext.DomHelper.append(workZone.getEl(), {tag:'div'}, true), {
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

            this.editPanel.add('north', new Ext.ContentPanel(Ext.DomHelper.append(workZone.getEl(), {tag:'div'}, true)));

            workZone.beginUpdate();
            var failure = function(o) {Ext.MessageBox.alert('Product Card',o.statusText);}
            var cb = {
                success : this.loadTabs.createDelegate(this),
                failure : failure,
                argument : {prod_id: prodId}
            };
            var con = new Ext.lib.Ajax.request('GET', Mage.url + '/mage_catalog/product/card/product/'+prodId+'/setid/'+setId+'/', cb);  
            
            workZone.add('south', new Ext.NestedLayoutPanel(this.editPanel, {closable: true, title:title}));
            workZone.endUpdate();
        },
        
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
                form.sendForm();
            }
        },
        
        
        loadTabs: function(response) {
            if (!this.editPanel) {
                return false;
            }            
            
            dataCard = Ext.decode(response.responseText);  
            this.editPanel.beginUpdate();
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
            },'-');
            
            // check if we have new product //
            if (response.argument.prod_id == 0) {
                if (dataCard.attribute_set.totalRecords > 1 ) {
                    var opts = [];
                    for (var i=0; i < dataCard.attribute_set.items.length; i++ ) {
                        var o = {tag: 'option',  value:dataCard.attribute_set.items[i].product_attribute_set_id, html:dataCard.attribute_set.items[i].product_set_code}
                        if (i == 0) {
                            o.selected = 'true';
                        }
                        opts.push(o);
                    }
                
                    var setSelect = Ext.DomHelper.append(this.editPanel.getEl(), {
                		tag:'select', children: opts
                    }, true);                
                    toolbar.add('Product type :', setSelect.dom);                   
               }
            }
           
           var panel = null;
            for(var i=0; i < dataCard.tabs.length; i++) {
                var panel = new Ext.ContentPanel('productCard_' + dataCard.tabs[i].name,{
                   title : dataCard.tabs[i].title,
                   autoCreate: true,
                   closable : false,
                   url: dataCard.tabs[i].url,
                   loadOnce: true,
                   background: true
               })
               var mgr = panel.getUpdateManager();
               mgr.on('update', this.onLoadPanel.createDelegate(this, [panel], true));
               this.editPanel.add('center', panel);
            }
            this.editPanel.endUpdate();
            return true;
        },
        
        onLoadPanel : function(el, response) {
             var i=0;
            // we can ignore panel.loaded - because next step set it to ture version Ext alpha 3r4
            panel = this.editPanel.getRegion('center').getPanel(el.id);
            if (form = Ext.DomQuery.selectNode('form', panel.getEl().dom))  {
                var el;             
                for(i=0; i < form.elements.length; i++) {
                    Ext.EventManager.addListener(form.elements[i], 'change', this.onFormChange.createDelegate(this, [panel], true));
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
        
        cancelNew: function() {
            
        } 
    }
}(Mage.Catalog);


