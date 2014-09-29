/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
//TODO: assemble all util methods in this module
define(function () {
    'use strict';

    return {

        /**
         * Generates a unique identifier.
         * @returns {String}
         * @private
         */
        uniqueid: function () {
            var idstr = String.fromCharCode((Math.random() * 25 + 65) | 0),
                ascicode;

            while (idstr.length < 5) {
                ascicode = Math.floor((Math.random() * 42) + 48);

                if (ascicode < 58 || ascicode > 64) {
                    idstr += String.fromCharCode(ascicode);
                }
            }

            return idstr;
        }
    }
});