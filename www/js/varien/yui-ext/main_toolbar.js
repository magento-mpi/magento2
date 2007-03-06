Ext.onReady(function(){
    var file = new Ext.menu.Menu({
        id: 'File',
        items: [
        	{        
        		text: 'New Customer',
        		handler: Ext.Varien.Menu.Action.Message
        	}, {
        		text: 'New Product',
        		handler: Ext.Varien.Menu.Action.Message
        	} , {
        		text: 'New Order',
        		handler: Ext.Varien.Menu.Action.Message
        	}, '-',  {
                text: 'Logout',
        		handler: Ext.Varien.Menu.Action.Logout
            }
		]
    });

    var edit = new Ext.menu.Menu({
        id: 'Edit',
        items: [
        	{        
        		text: 'Cut',
        		handler: Ext.Varien.Menu.Message
        	}, {
        		text: 'Copy',
        		handler: Ext.Varien.Menu.Message
        	} , {
        		text: 'Paste',
        		handler: Ext.Varien.Menu.Message
        	}, '-',  {
                text: 'Settings',
        		handler: Ext.Varien.Menu.Message
            }
		]
    });
    
    var help = new Ext.menu.Menu({
        id: 'Help',
        items: [
        	{        
        		text: 'Version 0.1',
        		handler: Ext.Varien.Menu.Message
        	}, '-' , {
        		text: 'About Magenta Commerce',
        		handler: Ext.Varien.Menu.Message
        	}
		]
    });


    var tb = new Ext.Toolbar('main_toolbar');
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
});

