/**
 * {license_notice}
 *
 * @category    frontend bundle product summary
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function ($, undefined) {
    "use strict";

    /**
     * Widget product Summary:
     * Handles rendering of Bundle options and displayes them in the Summary box
     */
    $.widget('mage.productSummary', {
        options: {
            mainContainer:           '#product_addtocart_form',
            templates: {
                summaryBlock:   '[data-template="bundle-summary"]',
                optionBlock:    '[data-template="bundle-option"]'
            },
            optionSelector:         '[data-container="options"]',
            summaryContainer:       '[data-container="product-summary"]'
        },
        /**
         * Method attaches event observer to the product form
         * @private
         */
        _create: function() {
            this.element
                .closest(this.options.mainContainer)
                .on('updateProductSummary', $.proxy(this._renderSummaryBox, this));
        },
        /**
         * Method extracts data from the event and renders Summary box
         * using jQuery templating mechanism
         * @param event
         * @param data
         * @private
         */
        _renderSummaryBox: function(event, data) {
            var config = data.config,
                summaryContainer;

            // Clear Summary box
            this.element.html("");

            $.each(config.selected, $.proxy(function(key, row) {
                if (row !== undefined) {
                    if (row.length > 0 && row[0] !== null) {
                        summaryContainer = this.element
                            .closest(this.options.summaryContainer)
                            .find(this.options.templates.summaryBlock)
                            .tmpl([{_label_: config.options[key].title}])
                            .appendTo(this.element);

                        $.each(row, $.proxy(function(rKey, option) {
                            var options = [];
                            if (!$.isArray(option)) {   // Regular options (single)
                                options.push({
                                    _quantity_: config.options[key].selections[option].qty,
                                    _label_: config.options[key].selections[option].name
                                });
                            } else {    // Used for Multi-select
                                $.each(option, function(index, value) {
                                    options.push({
                                        _quantity_: config.options[key].selections[value].qty,
                                        _label_: config.options[key].selections[value].name
                                    });
                                });
                            }
                            this.element
                                .closest(this.options.summaryContainer)
                                .find(this.options.templates.optionBlock)
                                .tmpl(options)
                                .appendTo(summaryContainer.find(this.options.optionSelector));
                        }, this));
                    }
                }
            }, this));
        }
    });
})(jQuery);