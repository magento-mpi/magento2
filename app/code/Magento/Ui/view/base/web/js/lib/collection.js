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

    var Collection = Scope.extend({

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
            _.each(this.items, function(config, name){
                registry.get(
                    config.injections,
                    this.initItem.bind(this, config, name)
                );
            }, this);
        },

        initItem: function(config, name){            
            var fullName    = this.name + '.' + name,
                injections  = Array.prototype.slice.call(arguments, 2) || [],
                component   = this.component,
                item;

            _.extend(config, {
                fullName: fullName,
                name: name,
                injections: injections,
                provider: this.provider,
            });

            item = new component(config);

            this.elems.push(item);

            registry.set(fullName, item);
        }
    });

    function init(constr, data, name, storage, provider){
        var config;

        config = _.extend({
            provider:   provider,
            name:       name,
            component:  constr,
        }, data.config, storage.get().layout[name]);

        registry.set(name, new Collection(config));
    }

    function load(constr, data, name){
        registry.get([
            'globalStorage',
            data.source
        ], init.bind(null, constr, data, name));    
    }

    return function(constr){
        return load.bind(null, constr);  
    };
});