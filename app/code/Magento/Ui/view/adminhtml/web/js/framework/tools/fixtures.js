define([
    'Magento_Ui/js/framework/tools/local_backend'
], function (LocalBackend) {
    
    var storage = LocalBackend.getStorage();
    var ROOT_PATH = 'Magento_Ui/js/framework/tools/fixtures';

    return {
        populate: function (namespace) {
            var name = namespace.replace(/(\.)/g, '_');
            
            require([ROOT_PATH + '/' + name], function (fixtures) {
                storage[namespace] = fixtures;
            });
        },

        empty: function (namespace) {
            storage[namespace] = [];
        }
    }
});