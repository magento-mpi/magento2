define([
    'underscore',
    'Magento_Ui/js/lib/collection',
    'Magento_Ui/js/lib/registry/registry'
], function(_, Collection, registry){
    'use strict';

    function init(constr, data, name){
        var provider = registry.get(data.source),
            storage  = registry.get('globalStorage'),
            layout   = storage.get().layout[name],
            config;

        config = _.extend({
            name:       name,
            component:  constr,
            layout:     layout,
            provider:   provider
        }, data.config);

        registry.set(name, new Collection(config));
    }

    function load(constr, data, name){
        var source  = data.source,
            args    = Array.prototype.slice.call(arguments),
            callback;

        callback = function(){
            init.apply(null, args);
        };

        registry.get(source, callback);
    }

    return function(constr){
        return load.bind(null, constr);
    };
});