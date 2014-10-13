/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events',
    'mage/utils'
], function(_, Collection, Scope, EventsBus, utils) {
    'use strict';

    var defaults = {
        label:      '',
        required:   false,
        template:   'ui/group/group',
        breakLine:  false,
        invalid: []
    };

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

    var FormGroup = Scope.extend({

        /**
         * Extends this with defaults and config.
         * Then calls initObservable, iniListenes and extractData methods.
         * 
         * @param  {Object} config
         */
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initListeners()
                .extractData();
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this.observe('invalid', _.isArray(this.invalid) ? this.invalid : []);

            return this;
        },

        /**
         * Extends instance with additional properties.
         * 
         * @return {Object} - reference to instance
         */
        extractData: function(){
            var elems = this.elems;

            return _.extend(this, {
                label:      getLabel(elems, this),
                required:   getRequired(elems, this),
                uid:        getUid(elems, this)
            });
        },

        onUpdate: function (shouldValidate, element, value) {
            var isValid = true;

            if (shouldValidate) {
                isValid = element.validate();
            }

            if (!isValid && this.invalid.hasNo(element)) {
                this.invalid.push(element);
            }

            this.trigger('update', element, value);
        },

        /**
         * Initializes instance's listeners.
         * 
         * @return {Object} - reference to instance
         */
        initListeners: function(){
            var update = this.onUpdate.bind(this);

            this.elems.forEach(function(element){
                element.on('update', update);
            });

            return this;
        },

        /**
         * Returns path(alias) to instance's template.
         * 
         * @return {String}
         */
        getTemplate: function(){
            return this.template;
        },

        /**
         * Returns true, if at least one of elements' value has changed.
         * 
         * @return {Boolean}
         */
        hasChanged: function(){
            return this.elems.some(function(elem){
                return elem.hasChanged();
            });
        }
    }, EventsBus);

    return Collection(FormGroup);
});