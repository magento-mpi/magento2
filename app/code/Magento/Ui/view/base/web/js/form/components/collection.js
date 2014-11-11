/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'mage/utils',
    'Magento_Ui/js/form/component',
    'underscore',
    'Magento_Ui/js/lib/registry/registry'
], function (utils, Component, _, registry) {
    'use strict';

    var __super__ = Component.prototype;

    var defaults = {
        lastIndex: 0,
        template: 'ui/form/components/collection'
    };

    var childTemplate = {
        template:   "{name}.{itemTemplate}",
        parent:     "{name}",
        name:       "{childIndex}",
        dataScope:  "{childIndex}"
    };

    return Component.extend({

        /**
         * Extends instance with default config, calls initialize of parent
         *     class, calls initChildren method
         */
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initChildren();
        },

        /**
         * Calls parent's initElement method, calls 'activate' method of elem,
         *     triggers 'update' event 
         * 
         * @param  {Object} elem
         */
        initElement: function (elem) {
            __super__.initElement.apply(this, arguments);

            elem.activate();

            this.trigger('update');
        },

        /**
         * Loops over corresponding data in data storage,
         *     creates child for each and pushes it's identifier to initialItems array
         *     
         * @return {Object} - reference to instance
         */
        initChildren: function () {
            var data     = this.provider.data,
                children = data.get(this.dataScope),
                initial  = this.initialItems = [];
                        
            _.each(children, function(item, index){
                initial.push(index);
                this.addChild(index);
            }, this);

            return this;
        },

        /**
         * Creates new item of collection, based on incoming 'index'.
         *     If not passed creates one with 'new_' prefix
         * 
         * @param {String|Object} index
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
         *     or if some child has been changed
         * 
         * @return {Boolean}
         */
        hasChanged: function(){
            var initial = this.initialItems,
                current = this.elems.pluck('index'),
                changed = !utils.identical(initial, current);

            return changed || this.elems.some(function(elem){
                return elem.delegate('hasChanged', 'some');
            });
        },

        /**
         * Creates function that removes element from collection using '_removeChild'
         *     method
         *     
         * @param  {Object} elem
         * @return {Function} - since this method is used by 'click' binding,
         *                      it requires function to invoke
         */
        removeChild: function(elem) {
            return function() {
                if (confirm(this.removeMessage)) {
                    this._removeChild(elem);
                }

            }.bind(this);
        },

        /**
         * Removes elememt from both collection and data storage,
         *     activates first element if removed one was active,
         *     triggers 'update' event
         * 
         * @param  {Object} elem
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

