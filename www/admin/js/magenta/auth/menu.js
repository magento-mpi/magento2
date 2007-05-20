Mage.Menu_Auth = function(){
    return {
        init : function(){
            Mage.Menu_Core.add('-');
            Mage.Menu_Core.add({
                text: 'Users & Permissions',
                handler : Mage.Admin.callModuleMethod.createDelegate(Mage.Admin, ['auth', 'loadMainPanel'], 0)                
            });
        }
    }
}();