/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    '../component',
    'mage/utils'
], function(_, Collection, Component, utils) {
    'use strict';

    var defaults = {
        label:      '',
        required:   false,
        template:   'ui/group/group',
        breakLine:  false
    };

    var __super__ = Component.prototype;

    /**
     * Either takes label property of 'obj' or if undefined loops over 
     *     elements and returnes first label found.
     * 
     * @param  {Array} elems - array of elements
     * @param  {Object} obj - alternate source of label
     * @return {String} - label string
     */
    function getLabel(elems, obj){
        var label = obj.label;

        if(!label){
            elems.some(function(elem){
                return (label = elem.label);
            });
        }

        return label;
    }

    /**
     * Returns true if at least one of passed elements has it's required
     *     observable attribute set to true.
     *     
     * @param  {Array} elems
     * @return {Boolean}
     */
    function getRequired(elems){
        return elems.some(function(elem){
            return elem.required();
        });
    }

    /**
     * Loops over elems array and returnes first uid found.
     * 
     * @param  {Array} elems
     * @return {String}
     */
    function getUid(elems){
        var uid;

        elems.some(function(elem){
            return (uid = elem.uid);
        });

        return uid;
    }

    var FormGroup = Component.extend({

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

        initElement: function(elem){
            __super__.initElement.apply(this, arguments);

            elem.on('update', this.onUpdate.bind(this));

            this.extractData();

            return this;
        },

        /**
         * Extends instance with additional properties.
         * 
         * @return {Object} - reference to instance
         */
        extractData: function(){
            var elems = this.elems();

            return _.extend(this, {
                label:      getLabel(elems, this),
                required:   getRequired(elems, this),
                uid:        getUid(elems, this)
            });
        },

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
        }
    });

    return Collection(FormGroup);
});