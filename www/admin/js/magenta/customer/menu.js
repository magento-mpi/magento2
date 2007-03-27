Mage.Menu_Customer = function(){
    var menu;
    return {
        init : function(){
            menu = new Ext.menu.Menu({
                id: 'mainCustomerMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Manage Customers',
                        handler: Mage.Customer.loadMainPanel.createDelegate(Mage.Customer)
                    })
                 ]
            });
            Mage.Core.addLeftToolbarItem({
                cls: 'x-btn-text-icon bmenu',
                text:'Customers',
                menu: menu
            });
        }
    }
}();
Mage.Menu_Customer.init();
