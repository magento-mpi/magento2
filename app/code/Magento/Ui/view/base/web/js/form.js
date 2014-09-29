/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/elements'
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/component',
    'underscore',
], function (elements, Scope, Component, _) {
    'use strict';

    function getConstructorFor(type) {
        return elements[type];
    }

    function isMetaDescriptor(obj) {
        return ('meta_ref' in obj) || ('input_type' in obj);
    }

    function formatMeta(obj, meta) {
        var result = {},
            initial,
            intermediate,
            reference;

        for (var name in obj) {
            initial = obj[name];

            if (isMetaDescriptor(initial)) {
                intermediate = result[name] = {};

                if (reference = initial['meta_ref']) {
                    _.extend(initial, meta[reference]);
                    delete initial['meta_ref'];
                }

                intermediate['constr'] = getConstructorFor(initial['input_type']);    
                intermediate['meta']   = initial;
                result[name]           = intermediate;    
            } else {
                result[name] = formatMeta(initial, meta);            
            }
        }

        return result;
    }

    var Form = Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * @param  {Object} config
         */
        initialize: function (config, provider) {
            _.extend(this, config);

            this.provider = provider;

            this.initFields();
        },

        initFields: function () {
            var meta = this.provider.meta.get();

            this.fields = formatMeta(meta, meta);
        }
    });

    return Component({
        constr: Form
    });
});