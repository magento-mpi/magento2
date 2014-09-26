/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope'
], function (Scope) {
    'use strict';

    /**
     * Generates a unique identifier.
     * @returns {String}
     * @private
     */
    function uniqueid() {
        var idstr = String.fromCharCode((Math.random() * 25 + 65) | 0),
            ascicode;

        while (idstr.length < 10) {
            ascicode = Math.floor((Math.random() * 42) + 48);

            if (ascicode < 58 || ascicode > 64) {
                idstr += String.fromCharCode(ascicode);
            }
        }

        return idstr;
    };

    return Scope.extend({
        initialize: function (config) {
            _.extend(this, config);

            this.uid     = uniqueid();
            this.module  = config.module     || 'ui';
            this.tooltip = this.tooltip      || null;
            this.label   = this.config.label || '';
        },

        getTemplate: function () {
            return this.module + '/form/element/' + this.type;
        }
    });
});