/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/*jshint browser:true jquery:true expr:true*/
define([
    "jquery",
    "jquery/ui"
], function($){

    $.widget('mage.advancedSearch', {
        options: {
            ajaxSpinnerSelector: '#gr-please-wait',
            ajaxResultSelector: '#gr-type-specific-options'
        },

        /**
         * bind handlers
         * @private
         */
        _create: function() {
            this.element.on('change', $.proxy(this._ajaxUpdate, this));
            this.options.selectedOption && this.element.val(this.options.selectedOption).trigger('change');
        },

        /**
         * ajax call for search option list
         * @private
         */
        _ajaxUpdate: function() {
            $(this.options.ajaxSpinnerSelector).show();
            $.post(this.options.url, {type_id: this.element.val()}, $.proxy(function(data) {
                $(this.options.ajaxSpinnerSelector).hide();
                $(this.options.ajaxResultSelector).html(data);
            }, this));
        }
    });

    return $.mage.advancedSearch;
});