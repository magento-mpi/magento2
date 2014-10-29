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

    return Scope.extend({
        initialize: function(config, additional){
            _.extend(this, config, additional);

            this._elems     = [];
            this.provider   = registry.get(this.provider);

            this.initObservable()
                .initListeners();
        },

        initObservable: function(){
            this.observe({
                'containers': [],
                'elems':      []
            });

            return this;
        },

        initElement: function(elem){
            var containers = elem.containers;

            if(containers){
                containers.push(this);
            }

            return this;
        },

        initListeners: function(){
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

        remove: function (element) {
            var _elems   = this._elems,
                position = _elems.indexOf(element);

            _elems.splice(position, 1);
            this.elems.remove(element);

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