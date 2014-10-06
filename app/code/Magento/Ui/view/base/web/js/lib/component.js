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

    /**
     * Extends configuration that will be retrieved from the data provider
     * with configuration that is stored in a 'baseConfig' object.
     * @param {Object} provider - DataProvider instance.
     * @param {Object} baseConfig - Basic configuration.
     * @returns {Object} Resulting configurational object.
     */
    function getConfig(provider, baseConfig) {
        var configs     = provider.config.get('components'),
            storeConfig = configs[baseConfig.name] || {};

        return _.extend({
            provider: provider
        }, storeConfig, baseConfig);
    }

    /**
     * Creates new instance of a grids' component.
     * @param {Object} data -
            Data object that was passed while creating component initializer. 
     * @param {HTMLElement} el -
            Element upon which compononet is going to be initialized.
     * @param {Object} base -
            Basic configuration.
     */
    function init(data, el, base) {
        var providerName    = base.parent_name,
            component       = providerName + ':' + base.name,
            mainComponent   = providerName + ':' + providerName,
            deps            = [providerName];

        if (registry.has(component)) {
            return;
        }

        if (component !== mainComponent) {
            deps.push(mainComponent);
        }

        registry.get(deps, function(provider) {
            var config = getConfig(provider, base);

            registry.set(component, new data.constr(config));
        });
    }

    return function(data) {
        return init.bind(this, data);
    };
});