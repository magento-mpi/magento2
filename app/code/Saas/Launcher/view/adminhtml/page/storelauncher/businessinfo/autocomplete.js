/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    'use strict';
    $.widget("storeCreation.autocompleteEmail", {
        options: {
            bindedFields: 'input[id^=sender_email]'
        },

        _init: function() {
            this.bindedFields = $(this.options.bindedFields);
            this.sameFields = this._getSameFields(this.bindedFields);

            $(this.sameFields).on("keyup change", $.proxy(this._bindedFieldsUpdateHandler, this));
            this.element.on("keyup blur change", $.proxy(this._mainFieldUpdateHandler, this));
        },

        _getSameFields: function(bindedFields) {
            var mainField = this.element;
            return $.grep(bindedFields, function(elem, index) {
                return elem.value == mainField.val();
            });
        },

        _mainFieldUpdateHandler: function() {
            var elem = this.element;
            $.each(this.sameFields, function() {
                this.value = elem.val();
            });
        },

        _bindedFieldsUpdateHandler: function(event) {
            var elementId = $(event.target).attr('id');
            this.sameFields = $.grep(this.sameFields, function(elem, index) {
                return !(elem.id == elementId);
            });
        }
    });
})(window.jQuery);
