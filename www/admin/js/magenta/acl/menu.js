Mage.Menu_Acl = function(){
    var menu;
    return {
        init : function(){
            Mage.Menu_Core.add('-');
            Mage.Menu_Core.add({
                text: 'Permissions'                  
            });
        }
    }
}();
Mage.Menu_Acl.init();
