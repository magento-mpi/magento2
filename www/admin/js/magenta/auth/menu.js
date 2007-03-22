Mage.Menu_Auth = function(){
    return {
        init : function(){
            Mage.Menu_Core.add('-');
            Mage.Menu_Core.add({
                text: 'Permissions'                  
            });
        }
    }
}();
Mage.Menu_Auth.init();
