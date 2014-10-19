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

    return Scope.extend({
        initialize: function(config, name){
            _.extend(this, config);

            this._elems     = [];
            this.name       = name;
            this.provider   = registry.get(this.provider);

            this.initObservable();
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

        insert: function(elems, offset){
            var size    = elems.length,
                _elems  = this._elems,
                callback;
            
            if(typeof offset === 'undefined'){
                offset = -1;
            }

            if(offset < 0){
                offset += _elems.length + 1;
            }

            this._elems = utils.reserve(_elems, size, offset);
            callback    = this.insertAt.bind(this, offset);

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

        getTemplate: function(){
            return this.template || 'ui/collection';
        },

        hasChanged: function(){
            return false;
        }
    }, EventsBus);
});