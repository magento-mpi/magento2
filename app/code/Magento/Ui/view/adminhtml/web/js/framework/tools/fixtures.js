define([
    'Magento_Ui/js/framework/tools/local_backend'
], function (LocalBackend) {
    
    var storage = LocalBackend.getStorage();
    var ROOT_PATH = 'Magento_Ui/js/framework/tools/fixtures';

    return {
        populate: function (namespace) {
            namespace = namespace.replace(/(\.)/g, '_');
            
            require([ROOT_PATH + '/' + namespace], function (fixtures) {
                storage[namespace] = fixtures;
            });
        },

        empty: function (namespace) {
            storage[namespace] = [];
        }
    }
});