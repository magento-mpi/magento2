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
//                    new Ext.menu.Item({
//                        text: 'Orders2',
//                        handler: Mage.Sales2.loadMainPanel.createDelegate(Mage.Sales2)
//                    })
                 ]
            });
            Mage.Core.addLeftToolbarItem({
                cls: 'x-btn-text .btn-sales',
                text:'Sales',
                menu: menu
            });
        }
    }
}();
Mage.Menu_Sales.init();
