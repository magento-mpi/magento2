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

    var Form = Scope.extend({

        /**
         * Extends instance with config, initializes instances core properties;
         *     
         * @param  {Object} config
         */
        initialize: function (config) {
            _.extend(this, config);

            this.initProperties()
                .createElements(this.meta);
        },

        /**
         * Initializes instance's properties, also initializes 'invalid'
         *     array in params storage.
         *     
         * @return {Object} - reference to instance
         */
        initProperties: function () {
            var provider = this.provider;

            _.extend(this, {
                data:       provider.data.get(),
                meta:       provider.meta.get(),
                elements:   {}
            })

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
            var path = '';

            _.each(obj, function (element, name) {
                element     = obj[name];
                path        = basePath ? basePath + '.' + name : name;

                if (typeof element !== 'object') {
                    return;
                }

                ('input_type' in element)
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
            var type            = config.input_type,
                constr          = elements[type],
                element;

            _.extend(config, {
                name: name,
                type: type,
                value: utils.nested(this.data, name),
                provider: this.provider,
                globalStorage: this.globalStorage
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
            this.elements[name] = element;
            registry.set(name, element);
        },

        /**
         * Handler for submit action. Validates form and then, if form is valid,
         *     invokes 'submit' method.
         */
        onSubmit: function () {
            var isValid = this.validate();

            if (isValid) {
                this.submit();
            }
        },

        /**
         * Submits form
         */
        submit: function () {
            console.log('submitting form lalala')
        },

        isElementValid: function (element) {
            return element.validate(true);
        },

        /**
         * Validates each element and returns true, if all elements are valid.
         * 
         * @return {Boolean}
         */
        validate: function () {
            var isElementValid = this.isElementValid.bind(this);

            return _.every(this.elements, isElementValid);
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

            _.extend(config, {
                provider: provider,
                globalStorage: globalStorage
            });

            registry.set(config.name, new Form(config));
        });
    };
});