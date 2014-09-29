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
        elements    = require('Magento_Ui/js/form/elements'),
        utils       = require('mage/utils'),
        _           = require('underscore'),
        Fieldset    = require('Magento_Ui/js/form/fieldset');

    elements = _.extend({}, elements, {
        fieldset: Fieldset
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
                reference,
                config,
                value,
                path;

            initial = initial || meta;
            path    = basePath;

            for (var name in initial) {
                target = initial[name];
                path   = path ? basePath + '.' + name : name;

                if (this.isMetaDescriptor(target)) {

                    if (reference = target['meta_ref']) {
                        _.extend(target, meta[reference]);
                        delete target['meta_ref'];
                    }

                    config  = _.extend(target, { name: path });
                    value   = utils.nested(data, path);

                    this.initElement(config, value);
                } else {
                    this.initElements(target, path);            
                }
            }
        },

        initElement: function (config, value) {
            var type    = config['input_type'],
                constr  = elements[type],
                element = new constr(config, value, this.refs);

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