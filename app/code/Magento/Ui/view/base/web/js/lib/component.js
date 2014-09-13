/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/registry/registry'
], function(registry) {
    'use strict';

    function getConfig(provider, baseConfig) {
        var configs     = provider.config.get('components'),
            storeConfig = configs[baseConfig.name] || {};

        return _.extend({
            provider: provider
        }, storeConfig, baseConfig);
    }

    function init(data, el, base) {
        var parent  = base.parent_name,
            name    = parent + ':' + base.name,
            main    = parent + ':' + parent,
            deps    = [parent];

        if (registry.has(name)) {
            return;
        }

        if (name !== main) {
            deps.push(main);
        }

        registry.get(deps, function(provider) {
            var config = getConfig(provider, base);

            registry.set(name, new data.constr(config));
        });
    }

    return function(data) {
        return init.bind(this, data);
    };
});