/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events',
    'Magento_Ui/js/lib/registry/registry'
], function(_, Scope, EventsBus, registry) {
    'use strict';

    function reserve(container, items, position){
        if(typeof position === 'undefined'){
            position = -1;
        }

        if(position < 0){
            position = container.length + position + 1;
        }

        container.splice(position, 0, new Array(items));
        container = _.flatten(container);

        return position;
    }

    function loadEach(shift, elems, callback, ctx){
        var resolved;

        elems.forEach(function(elem, index){
            resolved = callback.bind(ctx, shift + index);

            registry.get(elem, resolved);
        });
    }

    return Scope.extend({
        initialize: function(config) {
            _.extend(this, config);

            this._elems = [];

            this.initObservable()
                .inject()
                .initListeners();
        },

        initObservable: function(){
            this.observe({
                containers: [],
                elems: []
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

        inject: function(elems, position){
            elems = elems || this.injections;

            position = reserve(this._elems, elems.length, position);

            loadEach(position, elems, this.insertAt, this);

            return this;
        },

        insertAt: function(index, elem){
            var _elems = this._elems;

            _elems[index] = elem;

            this.elems(_.compact(_elems));

            this.initElement(elem);
        },

        getTemplate: function(){
            return this.template;
        },

        hasChanged: function(){
            return false;
        }
    }, EventsBus);
});