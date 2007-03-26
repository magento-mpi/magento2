Mage.Menu_Auth = function(){
    return {
        init : function(){
            Mage.Menu_Core.add('-');
            Mage.Menu_Core.add({
                text: 'Users & Permissions',
                handler: Mage.Auth.loadMainPanel.createDelegate(Mage.Auth)
            });
        }
    }
}();
Mage.Menu_Auth.init();
