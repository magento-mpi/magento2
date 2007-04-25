Mage.Catalog_Product_Attributes = function(){
    var loaded = false;
    var Layout = null;
    return {

        westLayout : null,
        btnEdit : null,
        btnDelete : null,

        attributeGrid : null,
        attributeGridToolbar : null,
        attributeGridUrl : Mage.url + '/mage_catalog/product/attributeList/',
        attributesDeleteUrl : Mage.url + '/mage_catalog/product/attributedel/',
        attributesCommitUrl : Mage.url + '/mage_catalog/product/attributeCommit/',

        addGroupAttributes : Mage.url + '/mage_catalog/product/addGroupAttributes/',
        removeElementUrl : Mage.url + '/mage_catalog/product/removElement/',        
        saveSetUrl : Mage.url + '/mage_catalog/product/saveSet/',
        saveGroupUrl : Mage.url + '/mage_catalog/product/saveGroup/',
        setTreeUrl : Mage.url + '/mage_catalog/product/attributeSetTree/',

        setGrid : null,
        setGridUrl :  Mage.url + '/mage_catalog/product/attributeSetList/',

        editSetGrid : null,
        editSetGridUrl : Mage.url + '/mage_catalog/product/attributesetproperties/',

        init : function() {
            var Core_Layout = Mage.Core.getLayout();
            if (!Layout) {
                Layout =  new Ext.BorderLayout(Ext.DomHelper.append(Core_Layout.getEl(), {tag:'div'}, true), {
                    west: {
                        split:true,
                        initialSize : 300,
                        autoScroll:false,
                        collapsible:false,
                        titlebar:false
                    },
                    center : {
                        autoScroll : false,
                        titlebar : false,
                        hideTabs : false,
                        tabPosition : 'top'
                    }
                });

                this.westLayout = new Ext.BorderLayout(Layout.getRegion('west').getEl().createChild({tag:'div'}), {
                    center: {
                        split:true,
                        autoScroll:false,
                        collapsible:false,
                        titlebar:false
                    },
                    south : {
                        split:true,
                        hideWhenEmpty : true,
                        initialSize : 200,
                        autoScroll : false,
                        collapsible:false,
                        titlebar : false,
                        hideTabs : true
                    }
                });

         //       this.initSetGrid();

                this.initSetTree();
                
                Layout.beginUpdate();
                Layout.add('west', new Ext.NestedLayoutPanel(this.westLayout));
                Layout.endUpdate();

//                this.setGrid.getDataSource().load({params:{start:0, limit:10}});

                Core_Layout.beginUpdate();
                Core_Layout.add('center', new Ext.NestedLayoutPanel(Layout, {title:"Product Attributes",closable:false}));
                Core_Layout.endUpdate();
                
                this.loadAttributeGrid();

            } else {
                Mage.Core.getLayout().getRegion('center').showPanel(Layout);
            }
        },

        initSetGrid : function() {
            var dataRecord = Ext.data.Record.create([
            {name: 'id', mapping: 'product_attribute_set_id'},
            {name: 'name', mapping: 'product_set_code'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'product_attribute_set_id'
            }, dataRecord);

            var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.setGridUrl}),
                reader: dataReader,
                remoteSort: true
            });

            dataStore.setDefaultSort('product_id', 'desc');


            var colModel = new Ext.grid.ColumnModel([{
                header: "Set Code",
                sortable: false,
                dataIndex: 'name',
                editor: new Ext.grid.GridEditor(new Ext.form.TextField({
                    allowBlank: false
                }))
            }]);

            var Set = Ext.data.Record.create([
                {name: 'name', type: 'string'},
            ]);

            this.setGrid = new Ext.grid.Grid(Ext.DomHelper.append(Layout.getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                loadMask : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                autoSizeColumns: true,
                monitorWindowResize: true,
                enableColLock : false
            });

            this.setGrid.on('rowclick', this.onRowClick.createDelegate(this));
            this.setGrid.getSelectionModel().on('rowselect', function(){
                    this.btnEdit.enable();
                    this.btnDelete.enable();
                }.createDelegate(this)
            );

            this.setGrid.render();

            var gridHead = this.setGrid.getView().getHeaderPanel(true);
            var tb = new Ext.Toolbar(gridHead);
            tb.addButton ({
                text: 'Add',
                handler : this.onAdd.createDelegate(this),
                cls: 'x-btn-text-icon btn_add'
            });

            this.btnEdit = tb.addButton({
                text: 'Edit',
                enableToggle : true,
                handler : this.onEditToogle.createDelegate(this),
                cls: 'x-btn-text-icon btn_application_form_edit',
                disabled : true
            });

            this.btnDelete = tb.addButton ({
                text: 'Delete',
                cls: 'x-btn-text-icon btn_delete',
                disabled : true
            });
        },

        initSetTree : function() {
            
                var sseed = 0;
                var gseed = 0;   
            
                var sview = Ext.DomHelper.append(Layout.getEl().dom,
                      {cn:[{id:'main-tb'},{id:'sbody'}]}
                );
                
                // create the primary toolbar
                var tb = new Ext.Toolbar('main-tb');
                tb.add({
                    id:'add',
                    text:'Set',
                    handler : addSet,
                    cls:'x-btn-text-icon b btn_add',
                    tooltip:'Add a new Set to the product attributes'
                }, {
                    id:'group',
                    text:'Group',
                    disabled:true,
                    handler:addGroup,
                    cls:'x-btn-text-icon btn_add',
                    tooltip:'Add a new group to the selected component'
                },'-',{
                    id:'remove',
                    text:'Remove',
                    disabled:true,
                    handler:removeHandler.createDelegate(this),
                    cls:'x-btn-text-icon btn_delete',
                    tooltip:'Remove the selected item'
                },'-',{
                    id:'reload',
                    text:'Reload',
                    disabled:false,
                    handler:refreshTree,
                    cls:'x-btn-text-icon btn_arrow_refresh',
                    tooltip:'Remove the selected item'
                });
                
                // for enabling and disabling
                var btns = tb.items.map;                
                
                this.westLayout.beginUpdate();
                this.westLayout.add('center', new Ext.ContentPanel(sview, { 
                    autoScroll:true,
                    fitToFrame:true,
                    toolbar: tb,
                    resizeEl:'sbody'
                }));
                this.westLayout.endUpdate();
                
                var ctree = new Ext.tree.TreePanel('sbody', {
                    animate:true,
                    enableDD:true,
                    containerScroll: true,
                    lines:false,
                    rootVisible:false
                });                
                
                
                
                ctree.on('nodedragover', function(e){
                    if (!e.dropNode) {
                        if ((e.target.attributes.type == 'group' && e.point == 'append') ||
                            (e.target.attributes.type == 'attribute' && e.point != 'append')) { 
                            return true;
                        } else {
                            return false;
                        }
                    }  
                    
                    var na = e.dropNode.attributes;
                    var ta = e.target.attributes;
                    console.log(na.setId + ':' + na.type + ':' + ta.type + ':' + e.point + ':' + ta.isGeneral);
                    if (
                       (na.setId === ta.setId && na.type == 'group' && ta.type == 'set' && e.point == 'append') ||
                       (na.setId === ta.setId && na.type == 'group' && ta.type == 'group' && e.point != 'append') ||
                       (na.setId === ta.setId && na.type == 'attribute' && ta.type == 'group' && e.point == 'append') ||
                       (na.setId === ta.setId && na.type == 'attribute' && ta.type == 'attribute' && e.point != 'append')
                     ) {
                        return true;
                     } else {
                        return false;
                     }
                });
                
                ctree.on('beforenodedrop', function(e){
                    if (e.dropNode) {
                        return true;
                    }
                    var s = e.data.selections, r = [];
                    for(var i = 0, len = s.length; i < len; i++){
                        var attributeId = s[i].id; // s[i] is a Record from the grid
                        var res = [];
                        e.target.cascade(function(params){
                            //Ext.dump(this);
                            if (this.attributes.attributeId == params[0]) {
                                res.push(this);
                            }
                        }, node, [attributeId]);
                        
                        if (res.length == 0) {
                            var node = new Ext.tree.TreeNode({ // build array of TreeNodes to add
                                allowDrop:false,
                                allowDrag:true,
                                type : 'dropped',
                                cls : 'x-tree-node-loading',
                                text: s[i].data.attribute_code,
                                setId : e.target.attributes.setId,
                                attributeId : attributeId
                            });
                            r.push(node);
                        }
                    }

                    if (res.length == 0) {
                    
                        var conn = new Ext.data.Connection();
                    
                		conn.on('requestcomplete', function(conn, response, options) {
                		    var i = 0;
                		    try {
                    		    var result =  Ext.decode(response.responseText);
                    		    if (result.error === 0) { 
                                    for(i=0; i < r.length; i++) {
                                        r[i].attributes.type = 'attribute';
                                        r[i].attributes.attributeId = response.attributeId;
                                        r[i].ui.removeClass('x-tree-node-loading');
                                    }
                                } else {
                                    for(i=0; i < r.length; i++) {
                                        if (result.errorNodes.indexOf(r[i].attributes.attributeId) >= 0) {
                                            r[i].parentNode.removeChild(r[i]);
                                        }
                                    }   
                    		        Ext.MessageBox.alert('Error',result.errorMessage);
                    		    }
                		    } catch (e){
                                for(i=0; i < r.length; i++) {
                                    r[i].parentNode.removeChild(r[i]);
                                }   
                                Ext.MessageBox.alert('Critical Error', response.responseText);
                		    }
                		    
                        });

                        conn.on('requestexception', function() {
                        });
                        
                        var requestParams = {};
                        requestParams.groupId = e.target.attributes.groupId;
                        var groupAttributes = [];
                        for (var i in r){
                            if (r[i].attributes && r[i].attributes.attributeId) {
                                groupAttributes.push(r[i].attributes.attributeId);
                            }
                        }
                        
                        requestParams.attributes = Ext.encode(groupAttributes);
                        
                        conn.request( {
                            url: this.addGroupAttributes,
                            method: "POST",
                            params: requestParams
                        });
                    } else {
                        Ext.MessageBox.alert('Error', 'This attributes exists in sets');
                    }                     
                    e.dropNode = r;  // return the new nodes to the Tree DD
                    e.cancel = r.length < 1; // cancel if all nodes were duplicates
                }.createDelegate(this));    
                
                //ctree.el.addKeyListener(Ext.EventObject.DELETE, removeNode);
                
                var croot = new Ext.tree.AsyncTreeNode({
                    allowDrag:true,
                    allowDrop:true,
                    id:'croot',
                    text:'Sets',
                    cls:'croot',
                    loader:new Ext.tree.TreeLoader({
                        dataUrl: this.setTreeUrl
                    })
                });
                
                function refreshTree() {
                    croot.reload();
                }
                
                ctree.setRootNode(croot);
                ctree.render();
                croot.expand();                
                
                var sm = ctree.getSelectionModel();
                sm.on('selectionchange', function(){
                    var n = sm.getSelectedNode();
                    if(!n){
                        btns.remove.disable();
                        btns.group.disable();
                        return;
                     }
                     var a = n.attributes;
                     btns.remove.setDisabled(!a.allowDelete);
                     btns.group.setDisabled(!a.setId);
                });                
                
                // semi unique ids across edits
                function guid(prefix){
                    return prefix+(new Date().getTime());
                }                
                
                
                function addSet(){
                    var id = guid('s-');
                    var text = 'Set '+(++sseed);
                    var node = createSet(id, text);
                    node.expand(false, false);
                    node.select();
                    if (node.lastChild) {
                        node.lastChild.ensureVisible();
                    }
                    ge.triggerEdit(node);
                }                              
                
                function createSet(id, text, groups){
                    if (id="-1") {
                        var group = new Ext.tree.TreeNode({
                            text : 'General',
                            groupId: 0,
                            id : 'group10',
                            iconCls : 'group',
                            cls : 'group',
                            leaf : false,
                            allowDrop : true,
                            allowDrag : false,
                            type : 'group',
                            setId : id,
                            allowDelete : false,
                            expanded : true,
                            allowEdit : true
                        });
                    }
                    
                    var node = new Ext.tree.AsyncTreeNode({
                        text: text,
                        iconCls: 'set',
                        cls: 'set',
                        type:'set',                        
                        id: id,
                        setId:id,
                        children : groups || [],
                        allowDelete:true,
                        allowDrop : true,
                        allowDrag : true,
                        expanded:true,                       
                        allowEdit:true
                    });
                    node.appendChild(group);
                    croot.appendChild(node);
                    return node;
                }
                
                function removeHandler() {
                    var n = sm.getSelectedNode();
                    var a = n.attributes;
                    if (a.allowDelete) {
                        n.disable();
                        var conn = new Ext.data.Connection();
                    
                		conn.on('requestcomplete', function(conn, response, options) {
                		    var i = 0;
                		    var result =  Ext.decode(response.responseText);
                    		    if (result.error === 0) { 
                    		        if (a.type == 'group') {
                    		            while (n.childNodes.length) {
                    		                n.childNodes[0].id = n.parentNode.firstChild.id + '/attr:' + n.childNodes[0].attributes.attributeId;
                        		            n.parentNode.firstChild.appendChild(n.childNodes[0]);
                    		            }
                    		        }
                    		        n.parentNode.removeChild(n);
                    		    } else {
                    		        n.enable();
                    		        Ext.MessageBox.alert('Error', result.errorMessage);
                	       	    }
                        });

                        conn.on('requestexception', function() {
                            Ext.MessageBox.alert('Error', 'requestException');
                        });
                        
                        var requestParams = {};
                        requestParams.element = a.type;
                        switch (a.type) {
                            case 'set':
                                requestParams.setId = a.setId;
                                break;
                            case 'group':
                                requestParams.setId = a.setId;
                                requestParams.groupId = a.groupId;
                                break;
                            case 'attribute':
                                requestParams.setId = a.setId;
                                requestParams.groupId = a.groupId;
                                requestParams.attributeId = a.attributeId;
                                break;
                            default:
                                return false;
                        }

                        conn.request( {
                            url: this.removeElementUrl,
                            method: "POST",
                            params: requestParams
                        });                
                    }
                }
            
            // create the editor for the component tree
            var ge = new Ext.tree.TreeEditor(ctree, {
                allowBlank:false,
                blankText:'A name is required',
                selectOnFocus:true
            });            
            
            ge.on('beforestartedit', function(){
                if(!ge.editNode.attributes.allowEdit){
                    return false;
                }
            });    
            
            ge.on('complete', function() {
                var node = ge.editNode;
                switch (node.attributes.type) {
                    case 'set':
                        var requestUrl = this.saveSetUrl;

                        var requestParams = {
                            code: ge.getValue(),
                            id: node.attributes.setId
                        };
                        
                        if (!requestParams.id) {
                            requestParams.groupCode = 'General';
                        }
                        break;
                    case 'group':
                        var requestUrl = this.saveGroupUrl;
                        var requestParams = {
                            code: ge.getValue(),
                            setId: node.attributes.setId,
                            id: node.attributes.groupId
                        };
                        break;
                    default:
                        return true;
                }
                
                var conn = new Ext.data.Connection();
                    
                conn.on('requestcomplete', function(conn, response, options) {
                    var i = 0;
                	var result =  Ext.decode(response.responseText);
                    if (result.error === 0) {
                        if (result.setId) {
                            node.attributes.setId = result.setId;
                        }
                        if (result.groupId) {
                            node.attributes.groupId = result.groupId;
                        }
                        node.enable();
           		    } else {
           		        node.parentNode.removeChild(node);
           		        Ext.MessageBox.alert('Error', result.errorMessage);
       	       	    }
               });

                conn.on('requestexception', function() {
                    Ext.MessageBox.alert('Error', 'requestException');
                });
                        
                conn.request( {
                    url: requestUrl,
                    method: "POST",
                    params: requestParams
                });                
            }.createDelegate(this));
            
            // add option handler
            function addGroup(btn, e){
                var n = sm.getSelectedNode();
                if ((typeof n.isLoaded == 'function') ) {
                    if (n.isLoaded()) {
                        var newnode = createGroup(n, 'Group'+(++gseed));
                        newnode.select();
                        ge.triggerEdit(newnode);
                    } else {
                        n.reload(addGroup);
                    }
                } else {
                    var newnode = createGroup(n, 'Group'+(++gseed));
                    //newnode.ui.addClass('x-tree-node-loading');
                    ge.triggerEdit(newnode);
                }
            }

            function createGroup(n, text){
                var snode = ctree.getNodeById('set:'+n.attributes.setId);

                var node = new Ext.tree.TreeNode({
                    text: text,
                    setId : n.attributes.setId,
                    iconCls:'folder',
                    type:'group',
                    allowDelete:true,
                    allowDrop : true,
                    allowDrag : true,
                    allowEdit : true,
                    id:guid('o-')
                });
                snode.appendChild(node);
                return node;
            }                     
        },

        loadAttributeGrid : function() {
            if (this.attributeGrid == null) {
                this.initAttributesGrid();
                Layout.beginUpdate();
                Layout.add('center', new Ext.GridPanel(this.attributeGrid));
                Layout.endUpdate();
                this.attributeGrid.getDataSource().load({params:{start:0, limit:10}});
            } else {
                this.attributeGrid.getDataSource().proxy.getConnection().url = this.attributeGridUrl;
                this.attributeGrid.getDataSource().load({params:{start:0, limit:10}});
            }
        },


        initAttributesGrid : function() {
            var dataRecord = Ext.data.Record.create([
                {name: 'attribute_id', mapping: 'attribute_id'},
                {name: 'attribute_code', mapping: 'attribute_code'},
                {name: 'data_input', mapping: 'data_input'},
                {name: 'data_type', mapping: 'data_type'},
                {name: 'required', mapping: 'required'},
                {name: 'filterable', mapping: 'filterable'},
                {name: 'searchable', mapping: 'searchable'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'attribute_id'
            }, dataRecord);

            var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.attributeGridUrl}),
                reader: dataReader,
                remoteSort: true
            });

            dataStore.setDefaultSort('attribute_code', 'asc');
            dataStore.on('update', this.onAttributeDataStoreUpdate.createDelegate(this));

            // shorthand alias
            var fm = Ext.form, Ed = Ext.grid.GridEditor;

            function formatBoolean(value){
                return value ? 'Yes' : 'No';  
            };            

            var data_types = [
                ['string', 'String'],
                ['int', 'Number'],
                ['decimal', 'Dec']
            ];
            
            var data_inputs = [
                ['hidden', 'Hidden'],
                ['text', 'Text'],
                ['textarea', 'Textarea'],
                ['select', 'ComboBox']
            ];
            

            var colModel = new Ext.grid.ColumnModel([{
                header: "ID#",
                sortable: true,
                locked:false,
                dataIndex: 'attribute_id'
            },{
                header: "Code",
                sortable: true,
                dataIndex: 'attribute_code',
                editor: new Ed(new fm.TextField({
                     allowBlank: false
               }))
            },{
                header: "Input type",
                sortable: true,
                dataIndex: 'data_input',
                editor: new Ed(new Ext.form.ComboBox({
                   typeAhead: false,
                   triggerAction: 'all',
                   mode: 'local',
                   store: new Ext.data.SimpleStore({
                        fields: ['type', 'value'],
                        mode : 'local',
                        data : data_inputs
                   }),
                   displayField : 'value',
                   valueField : 'type',
                   lazyRender:true
                }))                
            },{
                header: "Data type",
                sortable: true,
                dataIndex: 'data_type',
                editor: new Ed(new Ext.form.ComboBox({
                   typeAhead: false,
                   triggerAction: 'all',
                   mode: 'local',
                   store: new Ext.data.SimpleStore({
                        fields: ['type', 'value'],
                        mode : 'local',
                        data : data_types
                   }),
                   displayField : 'value',
                   valueField : 'type',
                   lazyRender:true
                }))                
            },{
                header: "Required",
                sortable: true,
                dataIndex: 'required',
                renderer: formatBoolean,
                editor: new Ed(new fm.Checkbox())
            },{
                header: "Searchable",
                sortable: true,
                dataIndex: 'searchable',
                renderer: formatBoolean,
                editor: new Ed(new fm.Checkbox())
            },{
                header: "Filterable",
                sortable: true,
                dataIndex: 'filterable',
                renderer: formatBoolean,
                editor: new Ed(new fm.Checkbox())
            }]);
            
            var ProductAttribute = Ext.data.Record.create([
               {name: 'attribute_id', type: 'string'},
               {name: 'attribute_code', type: 'string'},
               {name: 'data_input'},
               {name: 'data_type'},
               {name: 'required'}
            ]);

            this.attributeGrid = new Ext.grid.EditorGrid(Ext.DomHelper.append(Layout.getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                enableDragDrop : true,
                loadMask : true,
                autoSizeColumns : true,
                monitorWindowResize : true,
                ddGroup : 'TreeDD',
                trackMouseOver: false,
                autoHeight : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : false}),
                enableColLock : false
            });

            this.attributeGrid.render();
            var gridHead = this.attributeGrid.getView().getHeaderPanel(true);
            var tb = new Ext.Toolbar(gridHead);
            tb.addButton({
                text : 'New',
                cls: 'x-btn-text-icon btn_add',
                handler : function(){
                    var pa = new ProductAttribute({
                        attribute_id : '###',
                        attribute_code: 'new_attribute',
                        data_input: 'text',
                        data_type: 'decimal',
                        required : false
                    });
                    this.attributeGrid.stopEditing();
                    dataStore.insert(0, pa);
                    this.attributeGrid.startEditing(0, 1);
                }.createDelegate(this)
            });
            
            tb.addButton({
                text : 'Save',
                cls: 'x-btn-text-icon btn_accept',
                handler : function() {
                    this.attributeGrid.getDataSource().commitChanges();
                }.createDelegate(this)
            });
            
            tb.addButton({
                text : 'Delete',
                cls: 'x-btn-text-icon btn_delete',
                handler : function(){
                   var sm =  this.attributeGrid.getSelectionModel();        
                   if (sm.hasSelection()) {
                       var cell = sm.getSelectedCell();
                       rowIndex = cell[0];
                       var cb = {
                           success : this.onAttributeDeleteSuccess.createDelegate(this),
                           failure : this.onAttributeDeleteFailure.createDelegate(this),
                           argument : {rowIndex : rowIndex}
                       }
                       var record = this.attributeGrid.getDataSource().getAt(rowIndex);
                       Ext.lib.Ajax.request('POST', this.attributesDeleteUrl + 'attrId/'+ record.id +'/', cb);
                   }
                   
                }.createDelegate(this)
            });
            
            tb.addButton({
                text : 'Refresh',
                cls: 'x-btn-text-icon btn_arrow_refresh',
                handler : function() {
                    this.attributeGrid.getDataSource().load();
                }.createDelegate(this)
            });
        },
        
        onAttributeDeleteSuccess : function(response) {
            var datarep = Ext.decode(response);
            if (datarep.success) {
                var record = this.attributeGrid.getDataSource().getAt(response.argument.rowIndex);
                this.attributeGrid.getDataSource().remove(record);
            } else {
                Ext.MessageBox.alert('Attribute Grid', datarep.message);
            }
        },
        
        onAttributeDeleteFailure : function(response) {
            Ext.MessageBox.alert('Attribute Grid','Database Error');
        },   
        
        onAttributeDataStoreUpdate : function(store, record , operation) {
            var i = 0;
            if (operation  == Ext.data.Record.EDIT) {
                var conn = new Ext.data.Connection();
        		conn.on('requestcomplete', function(dm,response,option) {
		      	   record.commit();
        		});
		        conn.on('requestexception', function(dm, response, option, e) {
			         Ext.MessageBox.alert('Error', 'Your changes could not be saved. The entry will be rolled back.');
			         record.reject();
		        });
		        conn.request( {
                    url: this.attributesCommitUrl,
                    method: "POST",
                    params: {
                        id: record.id,
                        data : Ext.encode(record.data)
                    }
		        });                
            }
        },     

        loadMainPanel : function() {
            this.init();
        },

        loadEditSetGrid : function(setId) {
            if (this.editSetGrid == null) {
                this.initEditSetGrid(setId);
                this.westLayout.beginUpdate();
                this.westLayout.add('south', new Ext.GridPanel(this.editSetGrid));
                this.westLayout.endUpdate();
                this.editSetGrid.getDataSource().load();
            } else {
                this.editSetGrid.getDataSource().proxy.getConnection().url = this.editSetGridUrl +  'set/'+setId+'/';
                this.editSetGrid.getDataSource().load();
            }
        },

        initEditSetGrid : function(setId) {

            var dataRecord = Ext.data.Record.create([
                {name: 'name', mapping: 'name'},
                {name: 'value', mapping: 'value'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'name'
            }, dataRecord);

            var dataStore = new Ext.data.Store({
                proxy : new Ext.data.HttpProxy({url: this.editSetGridUrl +  'set/'+setId+'/'}),
                reader : dataReader
            });

            var colModel = new Ext.grid.ColumnModel([
                {header: "Name",  dataIndex: 'name'},
                {header: "Value", dataIndex: 'value',  editor: new Ext.grid.GridEditor(new Ext.form.TextField({allowBlank: false}))}
            ]);

            this.editSetGrid = new Ext.grid.EditorGrid(Ext.DomHelper.append(this.westLayout.getRegion('south').getEl().dom, {tag: 'div'}, true), {
                ds: dataStore,
                cm: colModel,
                loadMask : true,
                //selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                autoSizeColumns: true,
                monitorWindowResize: true,
                enableColLock : false
            });

            this.editSetGrid.render();

            var gridHead = this.editSetGrid.getView().getHeaderPanel(true);
            var tb = new Ext.Toolbar(gridHead);
            tb.addButton ({
                text: 'Save'
            });
        },

        onAdd : function() {
            this.btnEdit.enable();
            this.btnEdit.toggle(true);
            this.westLayout.getRegion('south').show();
            this.loadEditSetGrid(0);
        },

        onEditToogle : function(btn, e) {

            if (btn.pressed == true) {
                try {
                    row = this.setGrid.getSelectionModel().getSelected();
                    setId = row.id;
                } catch(e){
                    alert(e);
                };

                if (setId) {
                    this.loadEditSetGrid(setId);
                    this.westLayout.getRegion('south').show();
                } else {
                    alert('error');
                }
            } else {
                this.westLayout.getRegion('south').hide();
            }
        },


        onRowClick : function(grid, rowIndex, e) {
            var setId = 0;
            try {
                setId =  this.setGrid.getDataSource().getAt(rowIndex).id;
            } catch (e){};

            if (setId) {
                if (this.btnEdit.pressed == true) {
                    this.loadEditSetGrid(setId);
                } else {
                    this.loadAttributeGrid(setId)
                }
            } else {
                return false;
            }
            e.stopEvent();
        }

    }
}();
