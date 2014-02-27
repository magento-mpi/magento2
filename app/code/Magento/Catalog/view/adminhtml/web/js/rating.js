/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    'use strict';
    $.widget("marketing.ratingControl", {
        options: {
            colorFilled: '#333',
            colorUnfilled: '#CCCCCC',
            colorHover: '#f30'
        },

        _create: function() {
            this._labels = this.element.find('label');
            this._bind();
        },

        _bind: function() {
            this._labels.on({
                click: $.proxy(function(e) {
                    var elem = $(e.currentTarget);
                    $('#' + elem.attr('for')).attr('checked', 'checked');
                    this._updateRating();
                }, this),

                hover: $.proxy(function(e) {
                    this._updateHover($(e.currentTarget), this.options.colorHover);
                }, this),

                mouseleave: $.proxy(function(e) {
                    this._updateHover($(e.currentTarget), this.options.colorUnfilled);
                }, this)
            });

            this._updateRating();
        },

        _updateHover: function(elem, color) {
            elem.nextAll('label').andSelf().filter(function() {
                return !$(this).data('checked');
            }).css('color', color);
        },

        _updateRating: function() {
            var checkedInputs = this.element.find('input[type="radio"]:checked');
            checkedInputs.nextAll('label').andSelf().css('color', this.options.colorFilled).data('checked', true);
            checkedInputs.prevAll('label').css('color', this.options.colorUnfilled).data('checked', false);
        }
    });
})(jQuery);
