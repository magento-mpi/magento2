define([
    'Magento_Ui/js/lib/registry/registry'
], function(registry) {
    'use strict';

    function getConfig(provider, baseConfig) {
        var configs,
            storeConfig;

        configs = provider.config.get('components');
        storeConfig = configs[baseConfig.name];

        return _.extend({
            provider: provider
        }, storeConfig, baseConfig);
    }

    function getName(data) {
        return data.parent_name + ':' + data.name;
    }

    function init(data, el, base) {
        var name = getName(base);

        if (registry.has(name)) {
            return;
        }

        registry.get(base.parent_name, function(provider) {
            var config = getConfig(provider, base);

            registry.set(name, new data.constr(config));
        });
    }

    return function(data) {
        return init.bind(this, data);
    };
});