/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    '../component',
    'mage/utils'
], function(_, Component, utils) {
    'use strict';

    var defaults = {
        label:          '',
        required:       false,
        template:       'ui/group/group',
        fieldTemplate:  'ui/group/field',
        breakLine:      false
    };

    var __super__ = Component.prototype;

    return Component.extend({

        /**
         * Extends this with defaults and config.
         * Then calls initObservable, iniListenes and extractData methods.
         * 
         * @param  {Object} config
         */
        initialize: function() {
            _.extend(this, defaults);
            
            __super__.initialize.apply(this, arguments);
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('invalids', []);

            return this;
        },

        /**
         * Assignes onUpdate callback to update event of incoming element.
         * Calls extractData method.
         * @param  {Object} element
         * @return {Object} - reference to instance
         */
        initElement: function(element){
            __super__.initElement.apply(this, arguments);

            if (this.dataScope) {
                element.setDataScope(this.dataScope + '.' + element.dataScope);
            }
            element.on('update', this.onUpdate.bind(this));

            return this;
        },

        setDataScope: function (dataScope) {
            this.dataScope = dataScope;

            this.elems.each(function (element) {
                element.setDataScope(this.dataScope + '.' + element.index);
            }, this);
        },

        /**
         * Pushes invalid element to invalids array. Triggers update method on
         *     itself.
         * @param  {Object} element
         * @param  {Object} settings
         */
        onUpdate: function (element, settings) {
            var isValid = settings.isValid;

            if (!isValid && this.invalids.hasNo(element)) {
                this.invalids.push(element);
            }

            settings.element = element;
            this.trigger('update', this, settings);
        },

        /**
         * Returns true, if at least one of elements' value has changed.
         * 
         * @return {Boolean}
         */
        hasChanged: function(){
            return this.elems().some(function(elem){
                return elem.hasChanged();
            });
        },

        /**
         * Defines if group has more than one element.
         * @return {Boolean}
         */
        isMultiple: function () {
            return this.elems.getLength() > 1;
        },

        /**
         * Defines if group has only one element.
         * @return {Boolean}
         */
        isSingle: function () {
            return this.elems.getLength() === 1;
        }
    });
});