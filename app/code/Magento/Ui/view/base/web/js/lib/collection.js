/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/registry/registry'
], function (_, Scope, registry) {
    'use strict';

    return Scope.extend({
        initialize: function (config) {
            _.extend(this, config);

            this.initObservable()
                .initItems();
        },

        initObservable: function(){
            this.observe({
                'elems': []
            });

            return this;
        },

        initItems: function(){
            var config;
                
            _.each(this.layout, function(item, name){
                config = this.parseConfig(item);
                
                this.initItem(config, name);
            }, this);

            return this;
        },

        initItem: function(data, name){            
            var fullName    = this.name + '.' + name,
                injections  = Array.prototype.slice.call(arguments, 2) || [],
                component   = this.component,
                config,
                item,
                itemConfig = this.item_config;

            config = _.extend({
                provider:   this.provider,
                fullName:   fullName,
                name:       name
            }, data, itemConfig);

            item = new component(config);

            this.elems.push(item);

            registry.set(fullName, item);
        },

        parseConfig: function(item){
            var config = {};

            if (typeof item === 'string') {
                config.injections = item.split(' ');
            } else if (Array.isArray(item)) {
                config.injections = item;
            } else {
                _.extend(config, item);
            }

            config.injections = config.injections || [];

            return config;
        },

        getTemplate: function () {
            return 'ui/collection';
        }
    });
});