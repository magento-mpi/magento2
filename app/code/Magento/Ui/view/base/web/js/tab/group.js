/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './tab',
    'Magento_Ui/js/lib/collection',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/registry/registry'
], function(_, Tab, Collection, Scope, registry) {
    'use strict';

    var defaults = {
        collapsible:    false,
        opened:        true
    };

    var TabsGroup = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initTabs();
        },

        initObservable: function(){
            this.observe({
                'elems': [],
                'opened': this.opened
            });

            return this;
        },

        initTabs: function() {
            var fullName,
                item;

            _.each(this.items, function(config) {
                fullName = '.' + this.fullName + '.' + config.name;

                item = new Tab(config, this.provider);

                registry.set(fullName, item);

                this.elems.push(item);
            }, this);

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