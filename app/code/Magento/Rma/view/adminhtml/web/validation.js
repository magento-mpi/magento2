/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
(function (factory) {
    if (typeof define !== 'undefined' && define.amd) {
        define([
            'jquery',
            'jquery/ui',
            'mage/backend/validation'
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {
    "use strict";
    $.validator.prototype.checkForm = function () {
        this.prepareForm();
        var lastElements = [];
        for (var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++) {
            var className = $(elements[i]).attr('class');
            if (className.search(/rma-action-links-/i) != -1) {
                lastElements.push(elements[i]);
                continue;
            }
            this.check(elements[i]);
        }
        this.showErrors();
        for (var j = 0; lastElements[j]; j++) {
            this.check(lastElements[j]);
        }
        return this.valid();
    };
}));
