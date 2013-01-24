/**
 * {license_notice}
 *
 * @category    GiftRegistry
 * @package     enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";
    /**
     * Extending the rowBuilder widget and adding custom formProcessing for rendering recipients
     */
    $.widget('mage.giftRegistryCreate', $.mage.rowBuilder, {

        options: {
            rowIdPrefix: 'registrant:',
            rowCustomIdPrefix: 'registrant:custom:'
        },

        /**
         * @override
         * Process and loop through all row data to create preselected values. This is used for any error on submit.
         * For complex implementations the inheriting widget can override this behavior
         * @public
         * @param {Object} formDataArr
         */
        processFormDataArr: function(formDataArr) {
            var formData = formDataArr.formData;
            for (var i = this.options.rowIndex = 0; i < formData.length; this.options.rowIndex = i++) {
                this.addRow(i);
                var formRow = formData[i];
                this._processFormDataArrKey(i, formRow, false);
            }

        },

        /**
         * Function to recursively process json encoded form data
         * This utility helps in processing formData values which are objects/json themselves
         * @private
         * @param {Number} index - index of the curr row
         * @param {Object} formRow - row object containing field name and value
         * @param {Boolean} isCustom - if the registrant has custom fields
         *
         */
        _processFormDataArrKey: function(index, formRow, isCustom) {
            var idPrefix = isCustom ? this.options.rowCustomIdPrefix : this.options.rowIdPrefix;
            for (var key in formRow) {
                if (formRow.hasOwnProperty(key)) {
                    if (Object.prototype.toString.call(formRow[key]) === '[object Object]') {
                        this._processFormDataArrKey(index, formRow[key], true);
                    } else {
                        this.setFieldById(idPrefix + key + index, formRow[key]);
                    }
                }
            }
        }
    });
})(jQuery);