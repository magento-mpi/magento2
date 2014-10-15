/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'Magento_Ui/js/initializer/collection',
    'underscore'
], function (Component, Collection, _) {
    'use strict';

    var Item = Component.extend({
        initialize: function (config) {
            _.extend(this, config);

            console.log(this);
        },

        getTemplate: function () {
            return 'ui/form/components/collection';
        }
    });

    return Collection(Item);
});