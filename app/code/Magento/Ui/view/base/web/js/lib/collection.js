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
            var deps,
                callback,
                config;
                
            _.each(this.layout, function(item, name){
                config      = this.parseConfig(item);
                callback    = this.initItem.bind(this, config, name);
                deps        = config.injections;

                registry.get(deps, callback);
            }, this);

            return this;
        },

        initItem: function(data, name){            
            var fullName    = this.name + '.' + name,
                component   = this.component,
                injections  = Array.prototype.slice.call(arguments, 2),
                config,
                item;

            config = _.extend({
                provider:   this.provider,
                fullName:   fullName,
                name:       name,
                elems:      injections
            }, data);

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

            return config;
        },

        getTemplate: function () {
            return 'ui/collection';
        }
    });
});