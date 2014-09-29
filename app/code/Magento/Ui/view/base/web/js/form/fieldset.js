/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/elements',
    'Magento_Ui/js/lib/ko/scope',
    'underscore'
], function (controls, Scope, _) {
    'use strict';

    return Scope.extend({

        initialize: function (config, elements) {
            this.meta = config;

            this.initElements(elements);
        },

        initElements: function (elements) {
            this.observe('elements', _.map(elements, this.initElement, this));
        },

        initElement: function (value, name) {
            var meta    = this.meta[name],
                type    = meta['input_type'],
                constr  = controls[type],
                element = new constr(meta, value);

            return element;
        },

        getTemplate: function () {
            return 'ui/form/element/fieldset';
        }
    });
});