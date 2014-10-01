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
            _.each(this.layout.items, function(item, name){
                registry.get(
                    item.injections,
                    this.initItem.bind(this, item, name)
                );
            }, this);

            return this;
        },

        initItem: function(config, name){            
            var fullName    = this.name + '.' + name,
                component   = this.component,
                injections  = Array.prototype.slice.call(arguments, 2),
                item;

            _.extend(config, {
                provider:   this.provider,
                fullName:   fullName,
                name:       name,
                elems:      injections
            });

            item = new component(config);

            this.elems.push(item);

            registry.set(fullName, item);
        }
    });

    function init(constr, data, name, provider){
        var storage = registry.get('globalStorage'),
            layout  = storage.get().layout[name],
            config;

        config = _.extend({
            name:       name,
            component:  constr,
            layout:     layout,
            provider:   provider
        }, data.config || {});

        registry.set(name, new Collection(config));
    }

    function load(constr, data, name){
        registry.get([
            data.source
        ], init.bind(null, constr, data, name));    
    }

    return function(constr){
        return load.bind(null, constr);  
    };
});