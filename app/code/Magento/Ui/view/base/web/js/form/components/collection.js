/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
define([
    'underscore',
    'mage/utils',
    'Magento_Ui/js/lib/registry/registry',
    'Magento_Ui/js/form/component',
], function (_, utils, registry, Component) {
    'use strict';

    var childTemplate = {
        template:   "{name}.{itemTemplate}",
        parent:     "{name}",
        name:       "{childIndex}",
        dataScope:  "{childIndex}"
    };

    return Component.extend({
        defaults: {
            lastIndex: 0,
            template: 'ui/form/components/collection'
        },

        /**
         * Extends instance with default config, calls initialize of parent
         * class, calls initChildren method.
         */
        initialize: function () {
            this._super()
                .initChildren();

            return this;
        },

        /**
         * Activates the incoming child and triggers the update event.
         *
         * @param {Object} elem - Incoming child.
         */
        initElement: function(elem) {
            this._super();

            elem.activate();

            this.trigger('update');

            return this;
        },

        /**
         * Loops over corresponding data in data storage,
         * creates child for each and pushes it's identifier to initialItems array.
         *
         * @returns {Collection} Chainable.
         */
        initChildren: function() {
            var data     = this.provider.data,
                children = data.get(this.dataScope),
                initial  = this.initialItems = [];

            _.each(children, function(item, index) {
                initial.push(index);
                this.addChild(index);
            }, this);

            return this;
        },
        /**
         * Creates new item of collection, based on incoming 'index'.
         * If not passed creates one with 'new_' prefix.
         *
         * @param {String|Object} [index] - Index of a child.
         * @returns {Collection} Chainable.
         */
        addChild: function(index) {
            this.childIndex = !_.isString(index) ?
                ('new_' + this.lastIndex++) :
                index;

            this.renderer.render({
                layout: [
                    utils.template(childTemplate, this)
                ]
            });

            return this;
        },

        /**
         * Returnes true if current set of items differ from initial one,
         * or if some child has been changed.
         *
         * @returns {Boolean}
         */
        hasChanged: function(){
            var initial = this.initialItems,
                current = this.elems.pluck('index'),
                changed = !utils.identical(initial, current);

            return changed || this.elems.some(function(elem){
                return _.some(elem.delegate('hasChanged'));
            });
        },

        /**
         * Initiates validation of its' children components.
         *
         * @returns {Array} An array of validation results.
         */
        validate: function(){
            var elems;

            this.allValid = true;

            elems = this.elems.sortBy(function(elem){
                return !elem.active();
            });

            elems = elems.map(this._validate, this);

            return _.flatten(elems);
        },

        /**
         * Iterator function for components validation.
         * Activates first invalid child component.
         *
         * @param {Object} elem - Element to run validation on.
         * @returns {Array} An array of validation results.
         */
        _validate: function(elem){
            var result = elem.delegate('validate'),
                invalid;

            invalid = _.some(result, function(item){
                return !item.valid;
            });

            if(this.allValid && invalid){
                this.allValid = false;

                elem.activate();
            }

            return result;  
        },
        
        /**
         * Creates function that removes element
         * from collection using '_removeChild' method.
         * @param  {Object} elem - Element that should be removed.
         * @returns {Function}
         *      Since this method is used by 'click' binding,
         *      it requires function to invoke.
         */
        removeChild: function(elem) {
            return function() {
                var confirmed = confirm(this.removeMessage);

                if (confirmed) {
                    this._removeChild(elem);
                }

            }.bind(this);
        },

        /**
         * Removes elememt from both collection and data storage,
         * activates first element if removed one was active,
         * triggers 'update' event.
         *
         * @param {Object} elem - Element to remove.
         */
        _removeChild: function(elem) {
            var isActive = elem.active(),
                first;

            elem.destroy();

            first = this.elems.first();

            if (first && isActive) {
                first.activate();
            }

            this.trigger('update');
        }
    });
});

