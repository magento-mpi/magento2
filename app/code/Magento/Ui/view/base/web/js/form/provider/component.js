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

    function init(data, config, name){
        var constr = data.constr;

        registry.set(name, new constr(config));
    }

    return function(data) {
        return init.bind(this, data);
    };
});