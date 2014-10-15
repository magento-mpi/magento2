/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/form/collapsible'
], function(_, Collection, Collapsible) {
    'use strict';

    var __super__ = Collapsible.prototype;

    var TabsGroup = Collapsible.extend({
        initialize: function() {
            this.template = 'ui/tab';

            __super__.initialize.apply(this, arguments);
        }
    });
    
    return Collection(TabsGroup)
});