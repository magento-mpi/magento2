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
        _           = require('underscore');

    function getConstructorFor(type) {
        return elements[type];
    }

    function incrementPath(path, subpath) {
        return path + '.' + subpath;
    }

    var Form = Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * @param  {Object} config
         */
        initialize: function (config, provider) {
            _.extend(this, config);

            this.provider = provider;

            this.initElements();
        },

        initElements: function () {
            var meta = this.provider.meta.get(),
                data = this.provider.data.get();

            this._initElements(meta, data);

            return this;
        },

        _initElements: function (meta, data, initial, path) {
            var target,
                reference,
                config,
                value,
                constr;

            initial = initial || meta;
            path    = path    || '';

            for (var name in initial) {
                target = initial[name];
                path   = path + '.' + name;

                if (this.isMetaDescriptor(target)) {

                    if (reference = target['meta_ref']) {
                        _.extend(target, meta[reference]);
                        delete target['meta_ref'];
                    }

                    constr   = getConstructorFor(target['input_type']);    
                    config   = target;
                    value    = utils.byPath(data, path);

                    registry.set(path, new constr(config, value));
                } else {
                    this._initElements(meta, data, target, path);            
                }
            }
        },

        isMetaDescriptor: function (obj) {
            return ('meta_ref' in obj) || ('input_type' in obj);
        }
    });

    return Component({
        constr: Form
    });
});