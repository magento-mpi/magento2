/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'mage/utils',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events',
    'Magento_Ui/js/lib/registry/registry'
], function(_, utils, Scope, EventsBus, registry) {
    'use strict';
    
    function loadEach(elems, callback){
        elems.forEach(function(elem, index){
            registry.get(elem, function(elem){
                callback(index, elem);
            });
        });
    }

    function getOffsetFor(elems, offset){
        if(typeof offset === 'undefined'){
            offset = -1;
        }

        if(offset < 0){
            offset += elems.length + 1;
        }

        return offset;
    }

    function getProxy(callback, data){
        var conditions;

        if(_.isArray(data)){
            data = {
                conditions: "*",
                additional: data
            }
        }
        else if(!_.isObject(data)){
            data = {
                conditions: data,
                additional: []
            }
        }

        conditions = data.conditions;

        if(_.isUndefined(conditions)){
            data.conditions = '*';
        }

        data.callback = callback;

        return proxy.bind(null, data);
    }

    function proxy(data, value){
        var conditions = data.conditions,
            args;

        if(conditions === value || conditions === '*'){
            args = data.additional.slice();

            args.push(value);

            data.callback.apply(null, args);
        }
    }

    function parseSource(source, storages, data){
        var storage;

        source  = utils.template(source, data).split(':');
        storage = source.shift();

        return {
            source: source[0],
            storage: storages[storage]
        }
    }

    return Scope.extend({
        initialize: function(config, additional){
            _.extend(this, config, additional);

            this._elems     = [];
            this.provider   = registry.get(this.provider);

            this.initObservable()
                .initRenderer()
                .getLastPart('parentName', this.name)
                .getLastPart('parentScope', this.dataScope)
                .initListeners();
        },

        initObservable: function(){
            this.observe({
                'containers': [],
                'elems':      []
            });

            return this;
        },

        initRenderer: function () {
            this.renderer = registry.get('globalStorage').renderer;

            return this;
        },

        initListeners: function(){
            var listeners = this.listeners || {},
                params,
                iterator;

            _.each(listeners, function(handlers, source){
                params   = parseSource(source, this.provider, this);
                iterator = this.initListener.bind(this, params);

                _.each(handlers, iterator);
            }, this);

            return this;
        },

        initListener: function(params, data, callback){
            var storage = params.storage,
                source = params.source,
                value;

            callback = this[callback].bind(this);
            callback = getProxy(callback, data);

            value = storage.get(source);

            if(value){
                callback(value);
            }

            storage.on('update:' + source, callback);
        },

        initElement: function(elem){
            var containers = elem.containers;

            if(containers){
                containers.push(this);
            }

            return this;
        },

        getLastPart: function(container, ns){
            ns = ns.split('.');

            ns.pop();

            this[container] = ns.join('.');

            return this;
        },

        insert: function(elems, offset){
            var size    = elems.length,
                _elems  = this._elems,
                callback;
            
            offset      = getOffsetFor(_elems, offset);
            callback    = this.insertAt.bind(this, offset);
            this._elems = utils.reserve(_elems, size, offset);

            loadEach(elems, callback);

            return this;
        },

        insertAt: function(offset, index, elem){
            var _elems = this._elems;

            _elems[index + offset] = elem;
                
            this.elems(_.compact(_elems));
            this.initElement(elem);
        },

        remove: function (elem) {
            var _elems   = this._elems,
                position = _elems.indexOf(elem);

            _elems.splice(position, 1);
            this.elems.remove(elem);

            return this;
        },

        delegate: function(name, iterator){
            var method = this[name],
                args = _.toArray(arguments),
                result;

            if(typeof method === 'function'){
                result = method.apply(this, args.splice(1));
            }
            else{
                iterator = iterator || 'forEach';

                this.elems()[iterator](function(elem){
                    return (result = elem.delegate.apply(elem, args));
                });
            }

            return result;
        },

        getTemplate: function(){
            return this.template || 'ui/collection';
        }
    }, EventsBus);
});