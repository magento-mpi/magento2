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

        initialize: function (config, elements, refs) {
            _.extend(this, config);

            this.initElements(elements, refs);
        },

        initElements: function (elements, refs) {
            this.observe('elements', _.map(elements, this.initElement.bind(this, refs)));
        },

        initElement: function (refs, value, name) {
            var meta    = this.meta[name],
                type    = meta.input_type,
                constr  = controls[type],
                element = new constr(meta, value, refs);

            return element;
        },

        getTemplate: function () {
            return 'ui/form/fieldset';
        }
    });
});