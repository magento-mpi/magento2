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
        collapsible: false,
        visible: false
    };

    var TabsGroup = Scope.extend({

        initialize: function(config) {
            _.extend(this, defaults, config);

            if(!this.collapsible){
                this.visible = true;
            }

            this.initObservable()
                .initTabs();
        },

        initObservable: function(){
            this.observe({
                'elems': [],
                'visible': this.visible
            });

            return this;
        },

        initTabs: function() {
            var fullName,
                item;

            _.each(this.items, function(config) {
                fullName = '.' + this.fullName + '.' + config.name;

                _.extend(config, {
                    fullName: fullName,
                    provider: this.provider
                });

                item = new Tab(config);

                this.elems.push(item)

                registry.set(fullName, item);
            }, this);

            return this;
        },

        isVisible: function(){
            return this.collapsible ? this.visible() : true
        },

        toggle: function() {
            var visible;

            if (!this.collapsible) {
                return;
            }

            visible = this.visible;

            visible(!visible());
        }
    });
    
    return Collection(TabsGroup)
});