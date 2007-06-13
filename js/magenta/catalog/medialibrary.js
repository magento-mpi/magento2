Mage.Medialibrary = function () {	
	return { 
		settings : {}, 
		tree : {},
		left_panel : {},
		saveSetUrl : Mage.url + "media/move",
		getFoldersUrl : Mage.url + 'media/folderstree',
		getFolderFilesUrl : Mage.url + 'media/filesgrid',
		addDirUrl : Mage.url + 'media/mkdir',
		delDirUrl : Mage.url + 'media/rm',
		
		
		init : function () {
			dialog = new Ext.LayoutDialog(Ext.DomHelper.append(document.body, {tag:'div'}, true), {
					title: "Mediabrowser",
		            modal: true,
		            width:800,
		            height:450,
		            shadow:true,
		            minWidth:500,
		            minHeight:350,
		            autoTabs:true,
		            proxyDrag:true,
		            // layout config merges with the dialog config
		            west: {
                        split:true,
                        initialSize: 200,
                        minSize: 150,
                        maxSize: 250,
                        titlebar: true,
                        collapsible: true,
                        animate: true,
                        autoScroll: false,
                        fitToFrame:true
                    },	                    
                    center: {
                        autoScroll:true,
                        alwaysShowTabs: false                     
                    },
                    east: {
                        split:true,
                        initialSize: 150,
                        minSize: 100,
                        maxSize: 250,
                        titlebar: true,
                        collapsible: true,
                        animate: true
                    }
		    });
		    dialog.addKeyListener(27, dialog.hide, dialog);
		    dialog.setDefaultButton(dialog.addButton("Close", dialog.hide, dialog));
			
			var layout = dialog.getLayout();
			var panel_id = Ext.id();
			var panel2_id = Ext.id();
			var panel3_id = Ext.id();
			    
			var cview = layout.getRegion('west').getEl().createChild({tag :'div', id:panel_id});
		    var tb = new Ext.Toolbar(cview.createChild());
		    tb.add({
		        id:'add',
		        text:'Add',
		        handler: function () { this.addNode(); }.createDelegate(this),
		        tooltip:'Add a new Component to the dependency builder'
		    },'-',{
		        id:'remove',
		        text:'Remove',
		        disabled:false,
		        handler: function () { this.deleteNode(); }.createDelegate(this),
		        tooltip:'Remove the selected item'
		    });
		    
		    
			var lview = layout.getRegion('center').getEl().createChild({tag :'div', id:panel2_id});
		    var ctb = new Ext.Toolbar(lview.createChild());
			this.sortSelect = Ext.DomHelper.append(dialog.body.dom, {
				tag:'select', children: [
					{tag: 'option', value:'text', selected: 'true', html:'Name'},
					{tag: 'option', value:'size', html:'File Size'},
					{tag: 'option', value:'mod_date', html:'Last Modified'}
				]
			}, true);
			this.sortSelect.on('change', this.sortImages , this, true);
			
			this.txtFilter = Ext.DomHelper.append(dialog.body.dom, {
				tag:'input', type:'text', size:'12'}, true);
				
			this.txtFilter.on('focus', function(){this.dom.select();});
			this.txtFilter.on('keyup', this.filter, this, {buffer:500});
			
			ctb.add('Filter:', this.txtFilter.dom, 'separator', 'Sort By:', this.sortSelect.dom);
			ctb.add('-',{
		        id:'del_item',
		        text:'Delete',
		        handler: function () { this.deleteItem(); }.createDelegate(this),
		        tooltip:'Remove the selected item'
		    });

			layout.beginUpdate();
            this.left_panel = layout.add('west', new Ext.ContentPanel(panel_id, {
            	toolbar: tb
            }));
            
            this.right_panel = layout.add('east', new Ext.ContentPanel(Ext.id(), {
            	title: 'Detailed Info',
            	autoCreate : true
            }));
            
//            layout.add('center', new Ext.ContentPanel(Ext.id(), {fitToFrame:true, closable:false, autoCreate : true}));
            var innerLayout = new Ext.BorderLayout(Ext.DomHelper.append(document.body, {tag:'div'}), {
            	alwaysShowTabs: false,
                south: {
                    split:true,
                    initialSize: 150,
                    minSize: 100,
                    maxSize: 300,
                    autoScroll:true,
                    collapsible:true,
                    titlebar: true,
                    alwaysShowTabs: false
                },
                center: {                	
                    autoScroll:true,
                    tabPosition: 'top',
                    alwaysShowTabs: false
                }                
            });
            var dashPanel = innerLayout.add('south', new Ext.ContentPanel('south'));
            this.center_panel = innerLayout.add('center', new Ext.ContentPanel(panel3_id, {            	
            	toolbar: ctb,
            	autoCreate : true
            }));
            
            layout.add('center', new Ext.NestedLayoutPanel(innerLayout));

            this.buildView(this.center_panel);
            this.loadSettings();
			
            layout.endUpdate();	            
		    dialog.show();
		    
		    this.dashboard = new Mage.FlexUpload({
				src: Mage.url+'../media/flex/reports.swf',
				flashVars: 'baseUrl='+Mage.url + '&languageUrl=flex/language&cssUrl=' + Mage.skin + 'flex.swf',
				width: '100%',
				height: '90%'
			}); 

			this.dashboard.on("load", function (e) { 
				this.dashboard.setConfig( {
					uploadFileField: 'upload_file',
					uploadUrl: Mage.url + 'media/upload',
					fileFilter: {name:"*.*", filter:"*.*"},
					uploadParameters : {
						destination_dir : this.tree.getSelectionModel().getSelectedNode().attributes.id
					}
				});
			}, this );
			console.log(Mage.url + 'media/upload');
			this.dashboard.on("afterupload", function(e) {
				 for( var i = 0; i < e.data.length; i++) {
					alert(e.data[i].name);
				 }				 
			} , this); 
			
			this.dashboard.apply(dashPanel.getEl());
		    
		    this.detailsTemplate = new Ext.Template(
				'<div class="details">' +
				'	<img src="{url}">' +
				'	<div class="details-info">' +
				'		<b>Image Name:</b>' +
				'		<span>{text}</span>' +
				'		<b>Size:</b>' +
				'		<span>{sizeString}</span>' +
				'		<b>Last Modified:</b>' +
				'		<span>{dateString}</span>' +
				'	</div>' +
				'</div>'
			);
			this.detailsTemplate.compile();
		},
		
		deleteItem : function () {			
			var selNode = this.view.getSelectedNodes()[0];
			
			var requestUrl = this.delDirUrl;
            var requestParams = {
            	node: this.tree.getSelectionModel().getSelectedNode().attributes.id + this.settings.folderSeparator + selNode.title
            };

	        var conn = new Ext.data.Connection();                    
            conn.on('requestcomplete', function(conn, response, options) {            	
                var result = Ext.decode(response.responseText);

	            if (result.error !== 0) {
                    Ext.MessageBox.alert('Error', result.error_message);
                }
           }.createDelegate(this));
           
            this.view.select(0);
            selNode.parentNode.removeChild(selNode);                	
                	
            conn.on('requestexception', function() {
                Ext.MessageBox.alert('Error', 'requestException');
            });
            
            conn.request( {
                url: requestUrl,
                method: "POST",
                params: requestParams
            });
		},
		
		showDetails : function(view, nodes){
			if (this.view.getSelectionCount() > 1) {
				this.right_panel.getEl().hide();
				this.detailsTemplate.overwrite(this.right_panel.getEl(), []);
				return false;
			}
			
			
		    var selNode = nodes[0];
			if (selNode && this.view.store.getCount() > 0){
				var data = this.lookup[selNode.id];				
	            this.right_panel.getEl().hide();
	            this.detailsTemplate.overwrite(this.right_panel.getEl(), data);
	            this.right_panel.getEl().slideIn('l', {stopFx:true,duration:.2});				
			} else {
			    this.right_panel.getEl().update('');
			}
		},
		
		deleteNode : function () {
			var selNode = this.tree.getSelectionModel().getSelectedNode();

			var requestUrl = this.delDirUrl;
            var requestParams = {
            	node: selNode.attributes.id
            };
	        var conn = new Ext.data.Connection();                    
            conn.on('requestcomplete', function(conn, response, options) {            	
                var result = Ext.decode(response.responseText);

	            if (result.error !== 0) {
                    Ext.MessageBox.alert('Error', result.error_message);
                }
           }.createDelegate(this));
           /**
            * @todo поправить что б отлавливало ивент
            */
			this.tree.getSelectionModel().selectPrevious();
            selNode.parentNode.removeChild(selNode);
                	
                	
            conn.on('requestexception', function() {
                Ext.MessageBox.alert('Error', 'requestException');
            });
            
            conn.request( {
                url: requestUrl,
                method: "POST",
                params: requestParams
            });
		},
		
		buildView : function (panel) {
			this.dataRecord = Ext.data.Record.create([
	            {name: 'text'},
	            {name: 'mod_date'},
	            {name: 'size'},
	            {name: 'url'}	            
	        ]);
	
	        var dataReader = new Ext.data.JsonReader({
	            root: 'data',
	            successProperty: 'error'		            
	        }, this.dataRecord);
	    
	        var store = new Ext.data.Store({
	            proxy: new Ext.data.HttpProxy({url: this.storeUrl}),
	            reader: dataReader
	        });
	        
			this.view = new Ext.View(panel.getEl().createChild({tag:'div'}),
				'<div class="thumbnail" id="{text}" title="{text}">' +
				'	<img src="{url}" title="{text}" />' +
				'	<span>{text}</span>' +
				'</div>', { 
					multiSelect: true, 
					store: store
			});
			
			var lookup = {};
		    var formatSize = function(size){
		    	if (!size) return "unknown";
		    	size = parseInt(size);
		        if(size < 1024) {
		            return size + " bytes";
		        } else {
		            return (Math.round(((size*10) / 1024))/10) + " KB";
		        }
		    };
		    this.view.prepareData = function(data){
		    	data.shortName = data.text;
		    	data.sizeString = formatSize(data.size);
		    	data.dateString = new Date(data.mod_date).format("m/d/Y g:i a");
		    	lookup[data.text] = data;
		    	return data;
		    }.createDelegate(this);
		    this.lookup = lookup;
			
			this.view.on('selectionchange', this.showDetails, this, {buffer:100});	  		        
	        this.view.store.proxy.getConnection().url = this.getFolderFilesUrl;	
		},
		
		loadSettings : function () {
			var requestUrl = Mage.url + 'media/loadsettings';
			var rootFolder;
	        var conn = new Ext.data.Connection();
            conn.on('requestcomplete', function(conn, response, options) {
                var result = Ext.decode(response.responseText);
             	
                this.settings.folderSeparator = result.directory_separator;
                this.settings.rootFolder = result.root_directory;
                
                this.buildTree(this.left_panel);
            }, this);

            conn.on('requestexception', function() {
                Ext.MessageBox.alert('Error', 'requestException');
            });
            
            conn.request( {
                url: requestUrl,
                method: "POST"                    
            });
                
		},
		
		buildTree : function (panel) {
			var Tree = Ext.tree;               
               
		    this.tree = new Tree.TreePanel(panel.getEl().createChild({tag:'div'}), {
		        animate:true, 
		        loader: new Tree.TreeLoader({dataUrl:this.getFoldersUrl}),
		        enableDD:true,
		        containerScroll: true
		    });	    
		    
			this.tree.getSelectionModel().on('selectionchange', function (sm, node) {
				this.view.store.load({
					params: {node : node.attributes.id}
				});
			}, this);
			
			this.tree.on('beforemove', function (tree, node, oldParent, newParent, index) {
				var name = node.id.match(/[\/\\]([^\/\\]+)$/);
				newVal = newParent.id + this.settings.folderSeparator + name[1];
				oldVal = node.id;
				if (newVal == oldVal) return true;
            	
            	var requestUrl = this.saveSetUrl;
                var requestParams = {
					current_object:oldVal,
					destination_object:newVal
                };
                var conn = new Ext.data.Connection();                    
                conn.on('requestcomplete', function(conn, response, options) {
                    var result =  Ext.decode(response.responseText);

                    if (result.error !== 0) {
                        node.text = oldVal;	   
                        node.id = node.parentNode.id + oldVal;                     
                        Ext.MessageBox.alert('Error', result.error_message);
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
			
		    var root = new Tree.AsyncTreeNode({
		        text: 'root',
		        draggable:false,
		        id: this.settings.rootFolder
		    });
		    this.tree.setRootNode(root);	    			    

		    this.tree.render();
		    root.expand();
		    root.select();		
		    
		    var ge = new Ext.tree.TreeEditor(this.tree, {
                allowBlank:false,
                blankText:'New Folder',
                selectOnFocus:true
            });            
            
            ge.on('beforestartedit', function(){
                if(!ge.editNode.attributes.allowEdit){
                    return false;
                }
            }); 
            
            ge.on('complete', function(ge, newVal, oldVal) {
            	if (newVal == oldVal) return true;
            	
            	var node = ge.editNode;
            	var requestUrl = this.saveSetUrl;
                var requestParams = {
					current_object:node.parentNode.attributes.id + this.settings.folderSeparator + oldVal,
					destination_object:node.parentNode.attributes.id + this.settings.folderSeparator + newVal
                };
                
                var conn = new Ext.data.Connection();                    
                conn.on('requestcomplete', function(conn, response, options) {
                    var result =  Ext.decode(response.responseText);

                    if (result.error !== 0) {                    
                        node.text = oldVal;	   
                        node.id = node.parentNode.id + oldVal;                     
                        Ext.MessageBox.alert('Error', result.error_message);
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
            });
		},
		
		addNode : function () {
			var selNode = this.tree.getSelectionModel().getSelectedNode();
			var tmp = " (" + (selNode.childNodes.length + 1) + ")";

			var node_name = 'New Folder' + tmp;
			var node = new Ext.tree.TreeNode({
	            text: node_name,
	            iconCls:'cmp',
	            cls:'cmp',
	            type:'cmp',
	            id: selNode.attributes.id + this.settings.folderSeparator + node_name,
	            allowDelete:true,
	            allowEdit:true		            
	        });
	        selNode.expand();
           	selNode.appendChild(node);
	        
	        var requestUrl = this.addDirUrl;
            var requestParams = {
            	node: selNode.attributes.id,
            	new_directory: node_name
            };
	        var conn = new Ext.data.Connection();                    
            conn.on('requestcomplete', function(conn, response, options) {
                var result = Ext.decode(response.responseText);
                
                if (result.error !== 0) {
                	node.parentNode.select();
                    node.parentNode.removeChild(node);
                    Ext.MessageBox.alert('Error', result.error_message);
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
		},
			
		sortImages : function(){
			var p = this.sortSelect.dom.value;
	    	this.view.store.sort(p, p != 'text' ? 'desc' : 'asc');
	    	this.view.select(0);
	    },
	    /*
	    reset : function(){
		    this.view.getEl().dom.scrollTop = 0;
		    this.view.store.clearFilter();
			this.txtFilter.dom.value = '';
			this.view.select(0);
		},
		*/
		filter : function(){
			var filter = this.txtFilter.dom.value;
			this.view.store.filter('text', filter);
			this.view.select(0);
		}
	}
}();