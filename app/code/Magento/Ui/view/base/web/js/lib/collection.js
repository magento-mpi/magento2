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
            
            _.each(this.layout.items, this.initItem.bind(this));
        },

        initObservable: function(){
            this.observe({
                'elems': []
            });

            return this;
        },

        initItem: function(config, name){            
            var fullName    = this.name + '.' + name,
                component   = this.component,
                item;

            _.extend(config, {
                provider:   this.provider,
                fullName:   fullName,
                name:       name
            });

            item = new component(config);

            this.elems.push(item);

            registry.set(fullName, item);
        }
    });

    function init(constr, config, name, provider){
        var storage = registry.get('globalStorage'),
            layout  = storage.get().layout[name];

        _.extend(config, {
            name:       name,
            component:  constr,
            layout:     layout,
            provider:   provider
        });

        registry.set(name, new Collection(config, provider));
    }

    function load(constr, data, name){
        registry.get([
            data.source
        ], init.bind(null, constr, data.config, name));    
    }

    return function(constr){
        return load.bind(null, constr);  
    };
});