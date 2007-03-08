Ext.UpdateManager.defaults.loadScripts  = true;
Ext.UpdateManager.defaults.disableCaching  = true;


Ext.Mage = new Object();

Ext.Mage.Menu = {
    menuCollection : new Ext.util.MixedCollection(true),
    
    loadCollection : function() {
        var menu = new Ext.menu.Menu({
            id: 'catalog',
            items: [{
                text: 'View Products',
            }, '-' ,{
                text: 'New Category',
            },{
                text: 'New Product',
            },{
                text: 'New Attribute',
            }]
        });
        this.menuCollection.add('catalog', menu);
        menu = new Ext.menu.Menu({
            id: 'customers',
            items: [{
                text: 'View Customers & Orders',
            }, '-' ,{
                text: 'Send mass email',
            }]
        });
        this.menuCollection.add('customers', menu);
        
        menu = new Ext.menu.Menu({
            id: 'system',
            items: [{
                text: 'Manage Blocks and Layouts',
            }]
        });
        this.menuCollection.add('system', menu);
    },
    
    getMenu: function(name) {
        return this.menuCollection.get(name);
    }
}

Ext.EventManager.onDocumentReady(Ext.Mage.Menu.loadCollection, Ext.Mage.Menu, true);	

Ext.Mage.Toolbar = {
    tb : null,
    ds : null,
    
    assignTo : function(region) {
        var newEl = Ext.DomHelper.insertFirst(
            region.getEl(),
            {tag: 'div'},
            true 
        );
        this.tb = new Ext.Toolbar(newEl);
        this.tb.add({
            cls: 'bmenu',
            text: 'Catalog',
            menu: Ext.Mage.Menu.getMenu('catalog')
        });
        this.tb.add({
            cls: 'bmenu',
            text: 'Customers',
            menu: Ext.Mage.Menu.getMenu('customers')
        });
        this.tb.add({
            cls: 'bmenu',
            text: 'System',
            menu: Ext.Mage.Menu.getMenu('system')
        });
        this.tb.add(new Ext.ToolbarButton({text:'User'}));
    },
    
    loadData : function() {
    }
}

Ext.Mage.Panels = {
    
        loadPanel : function() {
            
            
            var innerLayout = new Ext.BorderLayout(Ext.DomHelper.append(this.layout.getEl(), {tag:'div'}, true), {
                west: {
                    split:true,
                    autoScroll:true,
                    collapsible:true,
                    titlebar: true
	            },
    	        center: {
                    autoScroll:true
	            }
            });
            
            var westInnerLayout = new Ext.BorderLayout(Ext.DomHelper.append(this.layout.getEl(), {tag:'div'}, true), {
                center : {
                    autoScroll:true,
                    titlebar: false
                },
                south : {
                    split:true,                    
                    initialSize: 200,
                    minSize: 50,
                    maxSize: 400,
                    autoScroll:true,
                    collapsible:true,
                }
            });
            
            westInnerLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            westInnerLayout.add('south', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            
            innerLayout.add('west', new Ext.NestedLayoutPanel(westInnerLayout));
            innerLayout.add('center', new Ext.ContentPanel(Ext.id(), {autoCreate: true}));
            
            this.layout.add('center', new Ext.NestedLayoutPanel(innerLayout, {title: 'Blocks and Layouts'}));
        
        },
    
        init : function(config) {
            this.layout = null;
            Ext.apply(this, config);
            this.region = this.layout.getRegion('center');        
            this.loadPanel();    
        }
};

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
/*
Ext.Mage['top_menu'] = new Ext.Toolbar(Ext.DomHelper.insertFirst(this.layout.getRegion('north').getEl(),{tag:'div'},true),[
{"text":"Catalog","cls":"bmenu","menu":{"items":[{"text":"View Products"},"-",{"text":"New Product"}]}},
{"text":"Customers","cls":"bmenu","menu":{"items":[{"text":"View Customers and Orders"},"-",{"text":"New Customer"}]}},
{"text":"System","cls":"bmenu","menu":{"items":[{"text":"Manage Blocks and Layouts"}]}},
{"text":"User"}
]);
*/
	   		//Ext.Mage.Toolbar.assignTo(this.layout.getRegion('north'));
	   		Ext.Mage.Panels.init({layout: this.layout});
	   		new Ext.Toolbar(Ext.DomHelper.insertFirst(this.layout.getRegion('north').getEl(),{tag:'div'},true),[
{"text":"Catalog","cls":"bmenu","menu":{"items":[{"text":"View Products"},"-",{"text":"New Product"}]}},
{"text":"Customers","cls":"bmenu","menu":{"items":[{"text":"View Customers and Orders"},"-",{"text":"New Customer"}]}},
{"text":"System","cls":"bmenu","menu":{"items":[{"text":"Manage Blocks and Layouts"}]}},
{"text":"User"}]);

	   		
            this.layout.endUpdate();			
        },
};


var Admin = new Ext.Mage.Interface;
Ext.EventManager.onDocumentReady(Admin.init, Admin, true);	

