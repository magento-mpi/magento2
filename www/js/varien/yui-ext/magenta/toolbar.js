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