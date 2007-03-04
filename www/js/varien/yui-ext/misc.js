Ext.Varien = new Object;
Ext.Varien.Menu = new Object;
Ext.Varien.Menu.Message = function(item){
	Ext.Msg.alert('Menu Message','You clicked the "'+item.text+'" menu item.');
};

var dialog;
Ext.Varien.Blocks = function(){
    return {
		layout : null,    	
		panel  : null,
		chooser: null,
		
		edit: function(block) {
			this.layout = Admin.getLayout();
			this.layout.beginUpdate();
			this.panel = new Ext.ContentPanel('block:edit:' + block.id, {
				autoCreate: true, 
				title: block.text, 
				closable: true
			})
			this.layout.add('center', this.panel);
			Ext.Varien.Blocks.addToolbar(this.panel);
			this.layout.endUpdate();
		},
		
		choose : function (btn){
	    	if(!this.chooser){
    			this.chooser = new Ext.Varien.BlockChooser({
    				url:'get-images.php',
    				width:515, 
    				height:400
	    		});
	    	}
    		this.chooser.show(btn.getEl(), alert);
	    },
	    
		addToolbar: function (panel) {
			var newEl = document.createElement('div');
			panel.getEl().dom.appendChild(newEl);
			var tb = new Ext.Toolbar(newEl)
			var btn = new Ext.ToolbarButton({text:'Add Block'});
			btn.on('click', this.choose, btn);
			tb.add(btn);
			tb.add(new Ext.ToolbarButton({text:'Save'}));
			tb.add(new Ext.ToolbarButton({text:'Reload'}));
			tb.add(new Ext.Toolbar.Separator());
			tb.add(new Ext.ToolbarButton({text:'View',	toggleGroup: 'view_mode', enableToggle: true}));
			tb.add(new Ext.ToolbarButton({text:'Code',	toggleGroup: 'view_mode', enableToggle: true}));
		},
		
		
		
		modulesGrid: function() {
              
	        var Modules = Ext.data.Record.create([
    	        {name: 'name', mapping: 'name'}
	        ]);

    	    // create reader that reads into Product records
	        var reader = new Ext.data.JsonReader({
    	        root: 'modules',
        	    totalProperty: 'totalRecords',
	            id: 'module_id'
    	    }, Modules);


	        // create the Data Store
    	    ds = new Ext.data.Store({
        	    proxy: new Ext.data.HttpProxy({url: BASE_URL+'/modules/list/'}),
	            reader: reader,
    	        remoteSort: false
	        });
    	    ds.load();

	        // the DefaultColumnModel expects this blob to define columns. It can be extended to provide
    	    // custom or reusable ColumnModels
	        var colModel = new Ext.grid.ColumnModel([
    	        {header: "Block Name", sortable: true, dataIndex: 'name'},
	        ]);
		
			var gridHolder = document.createElement('div');
			gridHolder.id = 'dialog-add_block-west-grid';
			document.body.appendChild(gridHolder);

	        var grid = new Ext.grid.Grid(gridHolder.id, {
	   		    ds: ds,
    	        cm: colModel
		    });
		    
            return grid;          
		},
		
		
		
		modulesGridFilter : function(grid) {
			var gridHead = grid.getView().getHeaderPanel(true);
			var ds = grid.getDataSource();
			var cm = grid.getColumnModel();
			
			var paging = new Ext.Toolbar(gridHead); 
			
			var filter = Ext.get(paging.addDom({ // add a DomHelper config to the toolbar and return a reference to it
            	tag: 'input'
		          , type: 'text'
        		  , size: '30'
		          , value: ''
        		  , cls: 'x-grid-filter'
		    }).el);
		    
		    filter.on('keypress', function(e) { // setup an onkeypress event handler
				   if(e.getKey() == e.ENTER) // listen for the ENTER key
        			ds.load();
			    });
	    
		    filter.on('keyup', function(e) { // setup an onkeyup event handler
		      if(e.getKey() == e.BACKSPACE && this.getValue().length == 0) // listen for the BACKSPACE key and the field being empty
		        ds.load();
		    });
		    
		    filter.on('focus', function(){this.dom.select();}); // setup an onfocus event handler to cause the text in the field to be selected              

			ds.on('beforeload', function() {
			  ds.baseParams = { // modify the baseParams setting for this request
			    name : filter.getValue() // retrieve the value of the filter input and assign it to a property named filter (rename to suit your needs)
			  };
			});        
		},
		
		blocksGrid : function() {
			
        var Blocks = Ext.data.Record.create([
            {name: 'name', mapping: 'name'},
            {name: 'descr', mapping: 'descr'}
        ]);

        // create reader that reads into Product records
        var reader = new Ext.data.JsonReader({
            root: 'blocks',
            totalProperty: 'totalRecords',
            id: 'block_id'
        }, Blocks);


        // create the Data Store
        ds = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url: BASE_URL+'/block/getBlocks/'}),
            reader: reader,
            remoteSort: false
        });
        ds.load();

        // the DefaultColumnModel expects this blob to define columns. It can be extended to provide
        // custom or reusable ColumnModels
        var colModel = new Ext.grid.ColumnModel([
            {header: "Block Name", sortable: true, dataIndex: 'name'},
            {header: "Description", width: 285, sortable: true, dataIndex: 'descr'}
        ]);
		
		var gridHolder = document.createElement('div');
		gridHolder.id = 'dialog-add_block-center-grid';
		document.body.appendChild(gridHolder);
		

		// create the Grid
        var grid = new Ext.grid.Grid(gridHolder.id, {
            ds: ds,
            cm: colModel
        });
		return grid;			
		},
		
		blocksGridToolbar: function(grid) {
		    var gridFoot = grid.getView().getFooterPanel(true);
			var ds = grid.getDataSource();
			var cm = grid.getColumnModel();
		    // add a paging toolbar to the grid's footer
		    var paging = new Ext.PagingToolbar(gridFoot, ds, {pageSize: 25});
		    paging.add('-', {
        		pressed: true,
		        enableToggle:true,
        		text: 'Detailed View',
		        cls: 'x-btn-text-icon details',
		        toggleHandler: toggleDetails
		    });

		    // create a floating label with display info
		    var displayInfo = gridFoot.createChild({cls:'paging-info'});
		    ds.on('load', function(){
        		var count = ds.getCount();
		        var msg = count == 0 ?
        		    "No topics to display" :
		            String.format(
		               'Displaying topics {0} - {1} of {2}',
        		        paging.cursor+1, paging.cursor+count, ds.getTotalCount()    
		            );
	    	    displayInfo.update(msg);
		    });

		    function toggleDetails(btn, pressed){
//        		cm.getColumnById('topic').renderer = pressed ? renderTopic : renderTopicPlain;
//		        cm.getColumnById('last').renderer = pressed ? renderLast : renderLastPlain;
//		        grid.getView().refresh();
		    }			
		},
		
		selectBlock: function(e, node) {
			var newEl = document.createElement('div');
			document.body.appendChild(newEl);
//			if(!dialog){ // lazy initialize the dialog and only create it once
//                dialog = new Ext.LayoutDialog(newEl, { 
//                        modal:false,
//                        title: 'Add Block',
//                        width:750,
//                        height:400,
//                        shadow:true,
//                        minWidth:300,
//                        minHeight:300,
//                        west: {
//	                        split:true,
//	                        initialSize: 150,
//	                        minSize: 100,
//	                        maxSize: 250,
//	                        titlebar: false,
//	                        collapsible: false,
//	                        animate: false
//	                    },
//	                    center: {
//	                        autoScroll:true,
//	                        tabPosition: 'top',
//	                        closeOnTab: true,
//	                        alwaysShowTabs: false
//	                    }
//                });
//                dialog.addKeyListener(27, dialog.hide, dialog);
//                dialog.addButton('Add Block', this.addBlock, this);
//                dialog.addButton('Close', dialog.hide, dialog);
//                
//                var layout = dialog.getLayout();
//                layout.beginUpdate();
//
//				var modulesGrid = this.modulesGrid();                
//                layout.add('west', new Ext.GridPanel(modulesGrid));
//			 	modulesGrid.render();
//			 	this.modulesGridFilter(modulesGrid);
//		        modulesGrid.getSelectionModel().selectFirstRow();
//      
//      			var blockGrid = this.blocksGrid();
//	            layout.add('center', new Ext.GridPanel(blockGrid));
//	            blockGrid.render();
//   			 	this.blocksGridToolbar(blockGrid);
//   		        blockGrid.getSelectionModel().selectFirstRow();
//   		        
//	            layout.endUpdate();
    var dlg = new Ext.LayoutDialog(Ext.id(), {
		autoCreate : true,
		width:400,
		height:300,
		minWidth:400,
		minHeight:300,
		syncHeightBeforeShow: true,
		shadow:true,
        fixedcenter:true,
        center:{autoScroll:false},
		east:{split:true,initialSize:150,minSize:150,maxSize:250}
	});
	dlg.setTitle('Choose an Image');
	dlg.getEl().addClass('ychooser-dlg');
	dlg.addKeyListener(27, dlg.hide, dlg);
    // add some buttons
    this.ok = dlg.addButton('OK', this.doCallback, this);
    this.ok.disable();
    dlg.setDefaultButton(dlg.addButton('Cancel', dlg.hide, dlg));
    dlg.on('show', this.load, this);
	this.dlg = dlg;
	var layout = dlg.getLayout();
	
	// filter/sorting toolbar
	this.tb = new Ext.Toolbar(this.dlg.body.createChild({tag:'div'}));
	this.sortSelect = Ext.DomHelper.append(this.dlg.body.dom, {
		tag:'select', children: [
			{tag: 'option', value:'name', selected: 'true', html:'Name'},
			{tag: 'option', value:'size', html:'File Size'},
			{tag: 'option', value:'lastmod', html:'Last Modified'}
		]
	}, true);
	this.sortSelect.on('change', this.sortImages, this, true);
	
	this.txtFilter = Ext.DomHelper.append(this.dlg.body.dom, {
		tag:'input', type:'text', size:'12'}, true);
		
	this.txtFilter.on('focus', function(){this.dom.select();});
	this.txtFilter.on('keyup', this.filter, this, {buffer:500});
	
	this.tb.add('Filter:', this.txtFilter.dom, 'separator', 'Sort By:', this.sortSelect.dom);
	
	// add the panels to the layout
	layout.beginUpdate();
	var vp = layout.add('center', new Ext.ContentPanel(Ext.id(), {
		autoCreate : true,
		toolbar: this.tb,
		fitToFrame:true
	}));
	var dp = layout.add('east', new Ext.ContentPanel(Ext.id(), {
		autoCreate : true,
		fitToFrame:true
	}));
    layout.endUpdate();
	
	var bodyEl = vp.getEl();
	bodyEl.appendChild(this.tb.getEl());
	var viewBody = bodyEl.createChild({tag:'div', cls:'ychooser-view'});
	vp.resizeEl = viewBody;
	
	this.detailEl = dp.getEl();
	
	// create the required templates
	this.thumbTemplate = new Ext.Template(
		'<div class="thumb-wrap" id="{name}">' +
		'<div class="thumb"><img src="{url}" title="{name}"></div>' +
		'<span>{shortName}</span></div>'
	);
	this.thumbTemplate.compile();	
	
	this.detailsTemplate = new Ext.Template(
		'<div class="details"><img src="{url}"><div class="details-info">' +
		'<b>Image Name:</b>' +
		'<span>{name}</span>' +
		'<b>Size:</b>' +
		'<span>{sizeString}</span>' +
		'<b>Last Modified:</b>' +
		'<span>{dateString}</span></div></div>'
	);
	this.detailsTemplate.compile();	
    
    // initialize the View		
	this.view = new Ext.JsonView(viewBody, this.thumbTemplate, {
		singleSelect: true,
		jsonRoot: 'images',
		emptyText : '<div style="padding:10px;">No images match the specified filter</div>'
	});
    this.view.on('selectionchange', this.showDetails, this, {buffer:100});
    this.view.on('dblclick', this.doCallback, this);
    this.view.on('loadexception', this.onLoadException, this);
    this.view.on('beforeselect', function(view){
        return view.getCount() > 0;
    });
    Ext.apply(this, config, {
        width: 540, height: 400
    });
    
    var formatSize = function(size){
        if(size < 1024) {
            return size + " bytes";
        } else {
            return (Math.round(((size*10) / 1024))/10) + " KB";
        }
    };
    
    // cache data by image name for easy lookup
    var lookup = {};
    // make some values pretty for display
    this.view.prepareData = function(data){
    	data.shortName = data.name.ellipse(15);
    	data.sizeString = formatSize(data.size);
    	data.dateString = new Date(data.lastmod).format("m/d/Y g:i a");
    	lookup[data.name] = data;
    	return data;
    };
    this.lookup = lookup;
    
	dlg.resizeTo(this.width, this.height);
	this.loaded = false;
       	},
       	
       	addBlock: function() {
       	}
	}
}();

Ext.Varien.Menu.Action = function(){
    return {
		Logout: function(item) {
			Ext.Msg.confirm('Logout','Are you sure ?');
		},
		
		Message: function(item) {
			Ext.Msg.alert('Menu Message','You clicked the "'+item.text+'" menu item.');
		}
	}
}();

Ext.Varien.TreeSwitcher = new Object;

Ext.Varien.TreeSwitcher.ControlsRenderer = function(){};
Ext.Varien.TreeSwitcher.ControlsRenderer.prototype = {
	tpl : new Ext.DomHelper.Template('<a id="{id}" href="#"><span class="body">{name}<br><span class="desc">{desc}</span></span></a>'),

	loadTree: function (e, node, args) {
		
		// get main Layout and get my region where i worked with panels
		var layout = Admin.getLayout().getRegion('west').getActivePanel().getLayout();
		
		// information about panel and clicked button
		var data = args.data;
		// get Ext.Varien.TreeSwitcher - this
		var obj = args.obj;
		
		// PanelName for new panel (this.id - id of clicked button)
		var	panelName = 'controls:'+this.id;
		
		// get center region - container for panel
		var center = layout.getRegion('center');
		
		// get activePanel if exists make active
		var activePanel = center.getPanel('div:' + panelName);
		
		if (activePanel) {
			layout.showPanel(activePanel);
		}  else { // if panel not exists make check div container for this 
			if (!document.getElementById('div:'+panelName)) {
				// if container not found - make new
				var divHolder = document.createElement('div');
				divHolder.id = 'div:'+panelName;
				document.body.appendChild(divHolder);
			}
			
			// start update layout
			layout.beginUpdate();
			
			// trying to generate toolbar for this panel
			var tb = obj.createPaneltoolbar(divHolder.id, data); 
			data.panel.toolbar = tb;
			// create new panel with parameters, it is ajax panel - load contet from server 
			layout.add('center', new Ext.ContentPanel('div:' + panelName, data.panel));
			// after creation this panel is active
			layout.endUpdate();
		}
		
		// change button selection
		node = Ext.get(this.id);
		YAHOO.util.Dom.removeClass(node.dom.parentNode.getElementsByTagName('a'), 'selected');
		node.addClass('selected');
	},
	
	createPaneltoolbar : function (parentId, args) {
		if (!document.getElementById(parentId + ':toolbar')) {
			var divHolder = document.createElement('div');
			divHolder.id = parentId + ':toolbar'
			document.getElementById(parentId).appendChild(divHolder);
		}
		
    var file = new Ext.menu.Menu({
        id: 'File',
        items: [
        	{        
        		text: 'New Customer'
        	}, {
        		text: 'New Product'
        	} , {
        		text: 'New Order'
        	}, '-',  {
                text: 'Logout'
            }
		]
    });

    var edit = new Ext.menu.Menu({
        id: 'Edit',
        items: [
        	{        
        		text: 'Cut'
        	}, {
        		text: 'Copy'
        	} , {
        		text: 'Paste'
        	}, '-',  {
                text: 'Settings'
            }
		]
    });
    
    var help = new Ext.menu.Menu({
        id: 'Help',
        items: [
        	{        
        		text: 'Version 0.1'
        	}, '-' , {
        		text: 'About Pepper'
        	}
		]
    });
		
		res = document.getElementById(parentId + ':toolbar');
	    var tb = new Ext.Toolbar(parentId + ':toolbar');
    	tb.add(
    		{
	            cls: 'bmenu', // icon and text class
    	        text:'File',
        	    menu: file  // assign menu by instance
	        }, 
    		{
        	    cls: 'bmenu', // icon and text class
            	text:'Edit',
	            menu: edit  // assign menu by instance
    	    }, 
    		{
	            cls: 'bmenu', // icon and text class
    	        text:'Help',
        	    menu: help  // assign menu by instance
	        }
	    );	
	    
	    return tb;	
	},
	
	render : function(el, response, updateManager, callback){
		el.dom.innerHTML = '';

		var buttons = Ext.decode(response.responseText);
		
		for(var id in buttons) {
			var b = buttons[id];
			var ne = this.tpl.append(el.dom, b);
			Ext.EventManager.addListener(ne, 'click', this.loadTree, ne, {data:b, obj:this});
		}
	}
}