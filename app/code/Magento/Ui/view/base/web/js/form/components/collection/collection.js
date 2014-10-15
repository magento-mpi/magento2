/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/collection',
    'underscore'
], function (Collection,  _) {
    'use strict';

    var defaults = {
        active: 0,
        template: 'ui/form/components/collection'
    };

    var __super__ = Collection.prototype;

    return Collection.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        }
    });
});