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

    /**
     * Private methods.
     */
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
                additional: data
            }
        }

        _.defaults(data, {
            conditions: '*',
            additional: [],
            callback: callback
        });

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

        source  = utils.template(source, data);
        source  = source.split(':');

        storage = source.shift();

        return {
            source: source[0],
            storage: storages[storage]
        }
    }

    var Component = Scope.extend({
        initialize: function(config, additional){
            _.extend(this, config, additional);

            this._elems     = [];
            this.provider   = registry.get(this.provider);

            this.initObservable()
                .initRenderer()
                .initParts()
                .initProperties()
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

        initParts: function(){
            this.setLastPart('parentName', this.name)
                .setLastPart('parentScope', this.dataScope);

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
                source  = params.source;

            callback = this[callback].bind(this);
            callback = getProxy(callback, data);

            callback(storage.get(source));

            storage.on('update:' + source, callback);
        },

        initProperties: function () {
            return this;
        },

        initContainer: function(parent){
            this.containers.push(parent);

            return this;
        },

        initElement: function(elem){
            elem.initContainer(this);

            return this;
        },

        setLastPart: function(container, ns){
            var parts = ns.split('.');

            parts.pop();

            this[container] = parts.join('.');

            return this;
        },

        getTemplate: function(){
            return this.template || 'ui/collection';
        }
    }, EventsBus);
    

    /**
     * Elements manipulation methods.
     */
    _.extend(Component.prototype, {
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

        destroy: function(){
            var data    = this.provider.data,
                layout  = this.renderer.layout; 

            this.containers.each(function(parent){
                parent.remove(this);
            }, this);

            data.remove(this.dataScope);
            layout.clear(this.name);

            this.elems.each(function(child){ 
                child.destroy();
            });
        }
    });


    /**
     * Elements traversing methods.
     */
    _.extend(Component.prototype, {
        delegate: function(name, iterator){
            var method  = this[name],
                args    = _.toArray(arguments),
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

        trigger: function(){
            var args    = _.toArray(arguments),
                bubble  = EventsBus.trigger.apply(this, args),
                result;

            if(!bubble){
                return false; 
            }

            this.containers.each(function(parent) {
                result = parent.trigger.apply(parent, args);
                
                if (result === false) {
                    bubble = false;
                }
            });

            return !!bubble;
        }
    });

    return Component;
});