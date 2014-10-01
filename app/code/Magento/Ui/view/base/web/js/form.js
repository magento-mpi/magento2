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
        _           = require('underscore'),
        Collection  = require('Magento_Ui/js/form/collection');

    elements = _.extend({}, elements, {
        collection: Collection
    });

    function incrementPath(path, subpath) {
        return path + '.' + subpath;
    }

    var Form = Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * @param  {Object} config
         */
        initialize: function (config, refs) {
            _.extend(this, config);

            this.refs = refs;

            this.initElements = this.initElements.bind(
                this, 
                refs.provider.meta.get(),
                refs.provider.data.get()
            );

            this.initElements();
        },

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

        initElement: function (config) {
            var constr  = elements[config.type],
                element = new constr(config);

            this.register(element);
        },

        register: function (element) {
            registry.set(element.name, element);
        },

        isMetaDescriptor: function (obj) {
            return ('meta_ref' in obj) || ('input_type' in obj);
        }
    });

    return function (config) {
        registry.get([config.source, 'globalStorage'], function (provider, globalStorage) {

            registry.set(config.name, new Form(config, {
                provider: provider,
                globalStorage: globalStorage
            }));
        });
    };
});