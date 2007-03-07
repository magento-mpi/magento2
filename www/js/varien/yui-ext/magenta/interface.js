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
	   		Ext.Mage.Toolbar.assignTo(this.layout.getRegion('north'));
	   		Ext.Mage.Panels.init({layout: this.layout});
            this.layout.endUpdate();			
        },
};


var Admin = new Ext.Mage.Interface;
Ext.EventManager.onDocumentReady(Admin.init, Admin, true);	

