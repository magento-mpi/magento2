/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract'
], function (Abstract) {
    'use strict';

    var __super__ = Abstract.prototype;

    return Abstract.extend({

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.initialValue = !(this.value() === undefined);
            this.value(this.initialValue);
        }
    });
});