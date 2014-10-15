define([
    'underscore',
    'Magento_Ui/js/lib/collection',
    'Magento_Ui/js/lib/registry/registry'
], function(_, Collection, registry){
    'use strict';

    function init(Item, params, data, name){
        var provider    = registry.get(data.source),
            storage     = registry.get('globalStorage'),
            layout      = storage.get().layout[name],
            config,
            Constr  = (params && params.use) ? params.use : Collection;

        config = _.extend({
            name:       name,
            component:  Item,
            layout:     layout,
            provider:   provider
        }, data.config);

        registry.set(name, new Constr(config));
    }

    function load(constr, params, data, name){
        var source  = data.source,
            args    = Array.prototype.slice.call(arguments),
            callback;

        callback = function(){
            init.apply(null, args);
        };

        registry.get(source, callback);
    }

    return function(constr, params){
        return load.bind(null, constr, params);
    };
});