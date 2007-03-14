Mage.Core = function(){
    return {
        init : function() {
            alert('test');
        },
        
        getLayout : function() {
            return Mage.Collection.get('layout');
        }
    }
}();
