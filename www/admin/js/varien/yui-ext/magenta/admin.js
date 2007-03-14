Mage.Core = function(){
    return {
        init : function() {
            alert('test');
        }
    }
}();

Mage.Core_Catalog = function(prent){
    prent.init();            
    return {
        init : function() {

        }
    }
}(Mage.Core);

Ext.EventManager.onDocumentReady(Mage.Core_Catalog.init, Mage.Core_Catalog, true);