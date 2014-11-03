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
        breakLine:      true
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
         * Assignes onUpdate callback to update event of incoming element.
         * Calls extractData method.
         * @param  {Object} element
         * @return {Object} - reference to instance
         */
        initElement: function(element){
            __super__.initElement.apply(this, arguments);

            element.on('update', this.trigger.bind(this, 'update'));

            return this;
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
        },

        contains: function (ignored, element) {
            return this.elems.contains(element);
        }
    });
});