Mage.Menu_ACL = function(){
    return {
        init : function(toolbar){
           toolbar.addButton({
                cls: 'x-btn-text bmenu',
                text:'System Permissions',
                handler : Mage.Admin.callModuleMethod.createDelegate(Mage.Admin, ['permissions', 'loadMainPanel'], 0)                                                                
            });
        }
    }
}();