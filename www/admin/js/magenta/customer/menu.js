Mage.Menu_Customer = function(){
    var menu;
    return {
        init : function(toolbar){
            menu = new Ext.menu.Menu({
                id: 'mainCustomerMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Manage Customers',
                        handler : Mage.Admin.callModuleMethod.createDelegate(Mage.Admin, ['customer', 'loadMainPanel'], 0)
                    })
                 ]
            });
            toolbar.addButton({
                cls: 'x-btn-text .btn-customer',
                text:'Customers',
                menu: menu
            });
        }
    }
}();