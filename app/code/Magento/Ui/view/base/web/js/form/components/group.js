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

            this.observe('invalids', [])
                .observe('values', []);

            return this;
        },

        initElement: function(element){
            __super__.initElement.apply(this, arguments);

            element.on('update', this.onUpdate.bind(this));

            this.extractData().apply(element);

            return this;
        },

        setPath: function (path) {
            this.path = path;

            return this;
        },

        setData: function (values) {
            this.values(values);
            this.apply();

            return this;
        },

        apply: function (element) {
            var indexed = this.elems.indexBy('index'),
                values  = this.values.indexBy('index'),
                index,
                data,
                elements = element ? [element] : this.elems();

            elements.forEach(function (element) {
                index   = element.index;
                data    = values[index];

                element.name = this.pathFor(element);
                element.set(data ? data.value() : null);

            }, this);
        },

        pathFor: function (element) {
            return [this.path, element.index].join('.');
        },

        /**
         * Extends instance with additional properties.
         * 
         * @return {Object} - reference to instance
         */
        extractData: function(){
            var elems = this.elems();

            _.extend(this, {
                label:      getLabel(elems, this),
                required:   getRequired(elems, this),
                uid:        getUid(elems, this)
            });

            return this;
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
});