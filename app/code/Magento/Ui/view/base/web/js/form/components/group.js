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
        hidden:         false,
        label:          '',
        required:       false,
        template:       'ui/group/group',
        fieldTemplate:  'ui/group/field',
        breakLine:      true
    };

    var __super__ = Component.prototype;

    function extractData(container, field){
        var data,
            value;

        container.some(function(item){
            value = item[field];

            if(_.isFunction(value)){
                value = value();
            }

            return !item.hidden() && (data = value);
        });

        return data;
    }

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

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            return this.observe('hidden label required');
        },

        /**
         * Assignes onUpdate callback to update event of incoming element.
         * Calls extractData method.
         * @param  {Object} element
         * @return {Object} - reference to instance
         */
        initElement: function(element){
            __super__.initElement.apply(this, arguments);

            element.on({
                'update': this.trigger.bind(this, 'update'),
                'toggle': this.toggle.bind(this)
            });

            this.extractData();

            return this;
        },

        extractData: function(){
            var elems = this.elems();

            this.label(extractData(elems, 'label'));
            this.required(extractData(elems, 'required'));

            return this;
        },

        toggle: function(value){
            this.extractData()
                .hidden(value);
        },

        /**
         * Defines if group has only one element.
         * @return {Boolean}
         */
        isSingle: function () {
            return this.elems.getLength() === 1;
        },

        /**
         * Defines if group has multiple elements.
         * @return {Boolean}
         */
        isMultiple: function () {
            return this.elems.getLength() > 1;
        },

        contains: function (ignored, element) {
            return this.elems.contains(element);
        }
    });
});