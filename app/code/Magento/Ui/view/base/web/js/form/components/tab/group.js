/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './tab',
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/registry/registry'
], function(_, Tab, Collection, Scope, registry) {
    'use strict';

    var defaults = {
        collapsible:    false,
        opened:         true
    };

    var TabsGroup = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable();

            _.each(this.items, this.initTab, this);
        },

        initObservable: function(){
            this.observe({
                'elems': [],
                'opened': this.opened
            });

            return this;
        },

        initTab: function(config){
            var fullName,
                item;

            fullName = '.' + this.fullName + '.' + config.name;

            _.extend(config, {
                fullName: fullName,
                provider: this.provider
            });

            item = new Tab(config);

            registry.set(fullName, item);

            this.elems.push(item);
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