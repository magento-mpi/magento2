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

            _.bindAll(this, '_insertAt');

            this.initProperties()
                .initObservable()
                .initListeners();
        },

        /**
         * Ment to define various properties.
         *
         * @returns {Component} Chainable.
         */
        initProperties: function () {
            _.extend(this,{
                'parentName':   this.getPart(this.name, -2),
                'parentScope':  this.getPart(this.dataScope, -2),
                'provider':     registry.get(this.provider),
                'renderer':     registry.get('globalStorage').renderer,
                'containers':   [],
                '_elems':       []
            });

            return this;
        },

        /**
         * Initializes observable propertis.
         *
         * @returns {Component} Chainable.
         */
        initObservable: function(){
            this.observe({
                'elems': []
            });

            return this;
        },

        /**
         * Initializes storages listeners.
         *
         * @returns {Component} Chainable.
         */
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

        /**
         * Used as iterator for the listeners object.
         * Creates callbacks and assigns it to the specified storage.
         *
         * @param {Object} data -
                Data object that contains storage object and
                it's property name that should be listened.
         * @param {Object} params - Parameters of the callback. 
         * @param {String} callback - Callback's name.
         */
        initListener: function(data, params, callback){
            var storage = data.storage,
                source  = data.source;

            callback = this[callback].bind(this);
            callback = getProxy(callback, params);

            callback(storage.get(source));

            storage.on('update:' + source, callback);
        },

        /**
         * Called when current element was injected to another component.
         *
         * @param {Object} parent - Instance of a 'parent' component.
         * @returns {Component} Chainable.
         */
        initContainer: function(parent){
            this.containers.push(parent);

            return this;
        },

        /**
         * Called when another element was added to current component.
         *
         * @param {Object} elem - Instance of an element that was added.
         * @returns {Component} Chainable.
         */
        initElement: function(elem){
            elem.initContainer(this);

            return this;
        },

        /**
         * Splits incoming string and returns its' part specified by offset.
         *
         * @param {String} parts
         * @param {Number} [offset] 
         * @param {String} [delimiter=.]
         * @returns {String}
         */
        getPart: function(parts, offset, delimiter){
            delimiter   = delimiter || '.';
            parts       = parts.split(delimiter);
            offset      = getOffsetFor(parts, offset);

            parts.splice(offset, 1);
            
            return parts.join(delimiter) || '';
        },

        /**
         * Returns path to components' template.
         * @returns {String}
         */
        getTemplate: function(){
            return this.template || 'ui/collection';
        }
    }, EventsBus);
    

    /**
     * Elements manipulation methods.
     */
    _.extend(Component.prototype, {
        /**
         * Requests specified components to insert
         * them into 'elems' array starting from provided position.
         *
         * @param {String} elem - Name of the component to insert.
         * @param {Number} [offset=-1] - Position at which to insert elements.
         * @returns {Component} Chainable.
         */
        insert: function(elem, offset){
            var _elems  = this._elems,
                insert  = this._insertAt;
            
            offset = getOffsetFor(_elems, offset);

            _elems.splice(offset, 0, false);

            registry.get(elem, function(elem){
                insert(elem, offset);
            });

            return this;
        },

        /**
         * Inserts provided component into 'elems' array at a specified position.
         * @private
         *
         * @param {Object} elem - Element to insert.
         * @param {Number} index - Position of the element.
         */
        _insertAt: function(elem, index){
            var _elems = this._elems;

            _elems[index] = elem;
                
            this.elems(_.compact(_elems));
            this.initElement(elem);
        },

        /**
         * Removes specified element from the 'elems' array.
         *
         * @param {Object} elem - Element to be removed.
         * @returns {Component} Chainable.
         */
        remove: function(elem) {
            utils.remove(this._elems, elem);
            this.elems.remove(elem);

            return this;
        },

        /**
         * Destroys current instance along with all of its' children.
         */
        destroy: function(){
            var data    = this.provider.data,
                layout  = this.renderer.layout; 

            this.off();

            this.containers.forEach(function(parent){
                parent.remove(this);
            }, this);

            data.remove(this.dataScope);
            layout.clear(this.name);
            
            this.elems().forEach(function(child){ 
                child.destroy();
            });
        }
    });


    /**
     * Elements traversing methods.
     */
    _.extend(Component.prototype, {
        /**
         * Tries to call specified method of a current component,
         * otherwise delegates attempt to its' children.
         *
         * @param {String} target - Name of the method.
         * @param [...] Arguments that will be passed to method.
         * @returns {*} Result of the method calls. 
         */
        delegate: function(target){
            var args = _.toArray(arguments);

            target = this[target];

            if(_.isFunction(target)){
                return target.apply(this, args.slice(1));   
            }

            return this._delegate(args);
        },

        /**
         * Calls 'delegate' method of all of it's children components.
         * @private
         *
         * @param {Array} args - An array of arguments to pass to the next delegation call.
         * @returns {Array} An array of delegation resutls.
         */
        _delegate: function(args){
            var result;

            result = this.elems.map(function(elem){
                return elem.delegate.apply(elem, args);
            });

            return _.flatten(result);
        },

        /**
         * Overrides 'EventsBus.trigger' method to implement events bubbling.
         *
         * @param {String} name - Name of the event.
         * @param [...] Any number of arguments that should be to the events' handler.
         * @returns {Boolean} False if event bubbling was canceled.
         */
        trigger: function(){
            var args    = _.toArray(arguments),
                bubble  = EventsBus.trigger.apply(this, args),
                result;

            if(!bubble){
                return false; 
            }

            this.containers.forEach(function(parent) {
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