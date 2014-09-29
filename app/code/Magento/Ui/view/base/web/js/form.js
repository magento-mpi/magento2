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

    function getConstructorFor(type) {
        return type === 'fieldset' ? Fieldset : elements[type];
    }

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

            this.initElements(
                refs.provider.meta.get(),
                refs.provider.data.get()
            );
        },

        initElements: function (meta, data, initial, basePath) {
            var target,
                reference,
                config,
                value,
                constr,
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

                    constr   = getConstructorFor(target['input_type']);    
                    config   = target;
                    value    = utils.byPath(data, path);

                    config.name = path;

                    registry.set(path, new constr(config, value, this.refs));
                } else {
                    this.initElements(meta, data, target, path);            
                }
            }
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