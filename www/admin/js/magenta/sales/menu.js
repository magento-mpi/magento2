Mage.Menu_Sales = function(){
    var menu;
    return {
        init : function(toolbar){
            menu = new Ext.menu.Menu({
                id: 'mainSalesMenu',
                items: [
                    new Ext.menu.Item({
                        text: 'Orders',
                        handler : Mage.Admin.callModuleMethod.createDelegate(Mage.Admin, ['sales', 'loadMainPanel'], 0)                        
                    })
                 ]
            });
            toolbar.addButton({
                cls: 'x-btn-text .btn-sales',
                text:'Sales',
                menu: menu
            });
        }
    }
}();