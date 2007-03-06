var Admin = function(){
	var layout;
    return {
		init : function(){
			layout = new Ext.BorderLayout(document.body, {
	                    hideOnLayout: true,
	                    north: {
	                        split:false,
	                        titlebar: false
	                    },
	                    west: {
	                    	collapsedTitle: true,
	                        split:true,	   
	                        titlebar: true,                 	
	                    	autoScroll:false,
	                        collapsible: true,
                            animate: true
	                    },
	                    east: {
	                    	collapsedTitle: true,
	                        split:true,
	                        tabPosition: 'top',
	                        initialSize: 202,
	                        minSize: 175,
	                        maxSize: 400,
	                        titlebar: true,
	                        collapsible: true,
                            animate: true
	                    },
	                    center: {
	                    	resizeTabs: true,
	                    	tabPosition: 'top',
	                        titlebar: true,
	                        autoScroll:true,
                            closeOnTab: true
                        },
		               south: {
        		            split:false,
                		    initialSize: 22,
		                    titlebar: false,
        		            collapsible: false,
                		    animate: false
		               }
	                });
                    layout.beginUpdate();
                    
	                layout.add('north', new Ext.ContentPanel('north', 'North'));
	                layout.add('east', new Ext.ContentPanel('autoTabs', {title: 'Auto Tabs', fitToFrame:true, closable: true}));
	                layout.add('center', new Ext.ContentPanel('dashboard', {title: 'Dashboard', fitToFrame:true, closable: true}));
	   				layout.add('south', new Ext.ContentPanel('status'));             
	                layout.getRegion('center').showPanel('dashboard');


					var catalogToolBar = new Ext.Toolbar('catalogToolBar');
					catalogToolBar.add({
						text: 'New',
						cls: 'x-btn-text'
					}, {
						text: 'Delete',
						cls: 'x-btn-text'
					});
					
					function onButtonClick(e) {
						
					};

	                var innerLayout = new Ext.BorderLayout('west', {
	                    center: {
	                    	resizeTabs: true,
//	                    	hideTabs: true,
	                        split:true,
	                        tabPosition: 'top',
	                        initialSize: 200,
	                        minSize: 100,
	                        maxSize: 400,
	                        autoScroll:true,
	                        collapsible:true,
	                        titlebar: false
	                    },
	                    south: { 	
	                    	collapsedTitle: true,
	                        split:true,
	                        tabPosition: 'top',
	                        initialSize: 200,
	                        minSize: 180,
	                        maxSize: 400,
	                        titlebar: true,
	                        collapsible: true,
                            animate: true
	                    }
	                });
	                
	               // innerLayout.add('center', new Ext.ContentPanel('west:center', {title:'Catalog'}));

					var	west_south_panel = new Ext.ContentPanel('west:south');
	                innerLayout.add('south', west_south_panel);
	                layout.add('west', new Ext.NestedLayoutPanel(innerLayout));
	                layout.endUpdate();	                
	                
	                // setup Controls panel
					var umgr = west_south_panel.getUpdateManager();
					umgr.setRenderer(new Ext.Varien.TreeSwitcher.ControlsRenderer);
					umgr.update({
						url: BASE_URL + '/index/treeSwitch/'
					 }); 					
	           },
	           
	           toggleWest : function(link){
	                var west = layout.getRegion('west');
	                if(west.isVisible()){
	                    west.hide();
	                    link.innerHTML = 'Show West Region';
	                }else{
	                    west.show();
	                    link.innerHTML = 'Hide West Region';
	                }
	           },
	           
	           getLayout : function() {
					return layout;
	           }
	     }
}();
Ext.EventManager.onDocumentReady(Admin.init, Admin, true);	

