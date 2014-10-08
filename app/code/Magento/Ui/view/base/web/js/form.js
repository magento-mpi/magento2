/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(function (require) {
    'use strict';

    var Scope       = require('Magento_Ui/js/lib/ko/scope'),
        Component   = require('Magento_Ui/js/lib/component'),
        registry    = require('Magento_Ui/js/lib/registry/registry'),
        elements    = require('Magento_Ui/js/form/element/index'),
        utils       = require('mage/utils'),
        _           = require('underscore');

    var defaults = {
        elements: [],
        validateOnSubmit: false
    };

    /**
     * Defines if an object is meta descriptor by checking if one has
     *     "meta_ref" or "input_type" properties defined
     *     
     * @param  {Object}  obj
     * @return {Boolean} - true, if object is meta descriptor
     */
    function isMetaDescriptor(obj) {
        var isMetaReference = obj.meta_ref,
            hasInputType    = obj.input_type;

        return isMetaReference || hasInputType;
    };

    var Form = Scope.extend({

        /**
         * Extends instance with config, initializes instances core properties;
         *     
         * @param  {Object} config
         */
        initialize: function (config) {
            _.extend(this, defaults, config);

            this.initElements()
                .initValidation();
        },

        /**
         * Initializes data and meta properties and invokes createElements
         *     method with single parameter this.meta.
         */
        initElements: function () {
            var provider = this.refs.provider;

            this.data = provider.data.get();
            this.meta = provider.meta.get();

            this.createElements(this.meta);

            return this;
        },

        /**
         * Recursively loops over 'obj' object's properties,
         *     checkes if property is meta descriptor, looks for "meta_ref"
         *     property, merges found reference object, builds config for element
         *     and invokes initElement with config.
         * If property is not meta descriptor, invokes itself with new obj(nested one) and path.
         *     
         * @param  {Object} obj - target object to format
         * @param  {String} basePath - path to obj (e.g. "customer.website")
         */
        createElements: function (obj, basePath) {
            var reference,
                path = '';

            _.each(obj, function (element, name) {
                element     = obj[name];
                path        = basePath ? basePath + '.' + name : name;

                if (typeof element !== 'object') {
                    return;
                }

                isMetaDescriptor(element) 
                    ? this.createElement(element, path)
                    : this.createElements(element, path);

            }, this);
        },

        /**
         * Formats config by merging corresponding meta data into it,
         *     looks up for corresponding constructor in elements object,
         *     creates an instance of it passing prepared config as a single parameter.
         * Invokes registerElement method passing instance and it's name to it.
         * 
         * @param  {Object} config - config for instance
         * @param  {Object} name - name for instance
         */
        createElement: function (config, name) {
            var metaReference   = config.meta_ref,
                type            = config.input_type,
                constr          = elements[type],
                element;

            if (metaReference) {
                _.extend(config, this.meta[metaReference]);
                delete config.meta_ref;
            }

            _.extend(config, {
                name: name,
                type: type,
                refs: this.refs,
                value: utils.nested(this.data, name)
            });

            delete config.input_type;

            element = new constr(config);
            this.registerElement(name, element);
        },

        /**
         * Registers element by it's name.
         * @param  {String} name
         * @param  {Object} element
         */
        registerElement: function (name, element) {
            this.elements.push(name);
            registry.set(name, element);
        },

        /**
         * If validateOnSubmit option is set to false, attaches 'validate' method
         *     as a listener to update event of all elements 
         */
        initValidation: function () {
            var provider = this.refs.provider.data,
                elements = this.elements,
                validate = this.validate.bind(this);

            if (!this.validateOnSubmit) {
                elements.forEach(function (name) {
                    provider.on('update:' + name, validate);
                });
            }
        },

        /**
         * Sets 'validated' property of params storage to false,
         *     so that all form groups invoke their validate methods.
         */
        validate: function () {
            this.refs.provider.params.set('validated', false);
        }
    });
    
    /**
     * Fetches for provider and globalStorage, extends config with references
     *     to those, creates instance of Form with config passed to constructor as
     *     a single parameter, writes instance of form to registry by it's name.
     *     
     * @param  {Object} config
     */
    return function (config) {
        registry.get([config.source, 'globalStorage'], function (provider, globalStorage) {
            config.refs = {
                provider: provider,
                globalStorage: globalStorage
            };

            registry.set(config.name, new Form(config));
        });
    };
});