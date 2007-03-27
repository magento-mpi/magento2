Mage.Menu_Sales = function(){
    var menu;
    return {
        init : function(){
            menu = new Ext.menu.Menu({
                id: 'mainSalesMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Orders',
                        handler: Mage.Sales.loadMainPanel.createDelegate(Mage.Sales)
                    })
                 ]
            });
            Mage.Core.addLeftToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Sales',
                menu: menu
            });
        }
    }
}();
Mage.Menu_Sales.init();
