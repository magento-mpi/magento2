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

    /**
     * Concatenates path and subpath by '.' separator
     * 
     * @param  {String} path
     * @param  {String} subpath
     * @return {String} result path
     */
    function incrementPath(path, subpath) {
        return path + '.' + subpath;
    }

    var Form = Scope.extend({

        /**
         * Extends instance with config, initializes observable properties,
         *     invokes initElements method.
         *     
         * @param  {Object} config
         */
        initialize: function (config) {
            _.extend(this, config);

            this.initElements = this.initElements.bind(
                this, 
                this.refs.provider.meta.get(),
                this.refs.provider.data.get()
            );

            this.initElements();
        },

        /**
         * Recursively loops over initial object's properties,
         *     checkes if property is meta descriptor, looks for "meta_ref"
         *     property, merges found reference object, builds config for element
         *     and invoces initElement with built config.
         * If property is not meta descriptor, invoces itself with new path and initial
         *     parameters.
         *     
         * @param  {Object} meta - reference to meta
         * @param  {Object} data - reference to data
         * @param  {Object} initial - target object to format
         * @param  {String} basePath - path to initial (e.g. "customer.website")
         */
        initElements: function (meta, data, initial, basePath) {
            var target,
                isObject,
                reference,
                value,
                path = '';

            initial = initial  || meta;

            for (var name in initial) {
                target      = initial[name];
                isObject    = typeof target === 'object';
                path        = basePath ? basePath + '.' + name : name;

                if (isObject && this.isMetaDescriptor(target)) {

                    if (reference = target['meta_ref']) {
                        _.extend(target, meta[reference]);
                        delete target['meta_ref'];
                    }
                    
                    _.extend(target, {
                        name: path,
                        type: target.input_type,
                        refs: this.refs,
                        value: utils.nested(data, path)
                    });

                    this.initElement(target);
                } else if (isObject) {
                    this.initElements(target, path);            
                }
            }
        },

        /**
         * Looks up for corresponding constructor in elements object,
         *     creates an instance of it passing config as a single parameted.
         * Invokes register method passing instance to it.
         * 
         * @param  {Object} config - config for instance
         */
        initElement: function (config) {
            var constr  = elements[config.type],
                element = new constr(config);

            this.register(element);
        },

        /**
         * Registes element by it's name to registry
         * 
         * @param  {Object} element
         */
        register: function (element) {
            registry.set(element.name, element);
        },

        /**
         * Defines if an object is meta descriptor by checking if one has
         *     "meta_ref" or "input_type" properties defined
         *     
         * @param  {Object}  obj
         * @return {Boolean} - true, if object is meta descriptor
         */
        isMetaDescriptor: function (obj) {
            return ('meta_ref' in obj) || ('input_type' in obj);
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