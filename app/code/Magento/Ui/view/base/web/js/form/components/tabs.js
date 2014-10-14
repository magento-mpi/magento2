/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/form/component',
    'Magento_Ui/js/lib/registry/registry'
], function(_, Collection, Component, registry) {
    'use strict';

    var defaults = {
        collapsible:    false,
        opened:         true,
        template:       'ui/tab'
    };

    var __super__ = Component.prototype;

    var TabsGroup = Component.extend({
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.observe({
                opened: this.opened
            });

            return this;
        },

        toggle: function() {
            var opened = this.opened;

            if (this.collapsible) {
                opened(!opened());
            }

            return this;
        }
    });
    
    return Collection(TabsGroup)
});