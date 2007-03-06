Ext.Mage = new Object();

Ext.Mage.Interface = function(){
};

Ext.Mage.Interface.prototype = {
		layout: null,
		
		init : function() {
			this.layout = new Ext.BorderLayout(document.body, {
				hideOnLayout: true,
	            north: {
                    split:false,
                    titlebar: false,
	                collapsible: false
	            },
	            center: {
	               resizeTabs: true,
                   alwaysShowTabs: true,
	               tabPosition: 'top',
	               titlebar: false,
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
	                
            this.layout.beginUpdate();
            this.layout.add('north', new Ext.ContentPanel('admin-north', {title: 'North'}));
	        this.layout.add('center', new Ext.ContentPanel('admin-center',  {title: 'CenterContent', fitToFrame:true, closable: true}));
	   		this.layout.add('south', new Ext.ContentPanel('admin-south'));
	   		this.addNorthToolbar();
            this.layout.endUpdate();			
        },
        
        addNorthToolbar : function() {
			var newEl = Ext.DomHelper.insertFirst(
                this.layout.getRegion('north').getEl(), 
                {tag: 'div'}, 
                true
            );
			var tb = new Ext.Toolbar(newEl)
			tb.add(new Ext.ToolbarButton({
			    text:'Add Block',
                handler: this.menuHandler
            }));
			tb.add(new Ext.ToolbarButton({text:'Save'}));
			tb.add(new Ext.ToolbarButton({text:'Reload'}));
			tb.add(new Ext.Toolbar.Separator());
			tb.add(new Ext.ToolbarButton({text:'View',	toggleGroup: 'view_mode', enableToggle: true}));
			tb.add(new Ext.ToolbarButton({text:'Code',	toggleGroup: 'view_mode', enableToggle: true}));
        },
        
        menuHandler: function(node, e, data) {
        }
};


var Admin = new Ext.Mage.Interface;
Ext.EventManager.onDocumentReady(Admin.init, Admin, true);	

