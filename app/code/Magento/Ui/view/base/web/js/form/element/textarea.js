/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './input'
], function (Input) {
    'use strict';

    var defaults = {
        cols: 15,
        rows: 2
    };

    return Input.extend({

        /**
         * Invokes initialize method of parent class.
         */
        initialize: function () {
            _.extend(this, defaults);

            Input.prototype.initialize.apply(this, arguments);
        }
    });
});