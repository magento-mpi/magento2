/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/registry/registry'
], function (registry) {
    'use strict';

    function createInstance(constr, config, provider) {
        config.provider = provider || null;

        registry.set(config.name, new constr(config));
    };

    function init(constr, el, config) {
        var provider    = config.provider,
            create      = createInstance.bind(null, constr, config);

        provider
            ? registry.get(provider, create)
            : create();
    };

    return function (constr) {
        return init.bind(null, constr);
    };
});