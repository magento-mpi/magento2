define([
    'Magento_Ui/js/framework/provider/model'
], function (Provider) {
    
    var ROOT_PATH = 'Magento_Ui/js/framework/tools/fixtures';

    return {
        populate: function (namespace) {
            var name = namespace.replace(/(\.)/g, '_');

            require([ROOT_PATH + '/' + name], function (fixtures) {
                Provider.get(namespace).done(function (component) {
                    component.client._adapter.backend.storage = fixtures;
                });
            });
        },

        empty: function (namespace) {
            Provider.get(namespace).done(function (component) {
                component.client._adapter.backend.storage = [];
            }); 
        }
    }
});