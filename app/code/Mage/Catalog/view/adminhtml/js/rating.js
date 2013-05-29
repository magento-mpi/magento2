/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    'use strict';
    $.widget("marketing.ratingControl", {
        options: {
            colorFilled: '#333',
            colorUnfilled: '#CCCCCC',
        },

        _create: function() {
            this._labels = this.element.find('label');
            this._bind();
        },

        _bind: function() {
            this._labels.on('click', $.proxy(function(e) {
                var elem = $(e.currentTarget);
                $('#' + elem.attr('for')).attr('checked', 'checked');
                this._updateRating();
            }, this));
            this._updateRating();
        },

        _updateRating: function() {
            var checkedInputs = this.element.find('input[type="radio"]:checked');
            checkedInputs.css('color', this.options.colorFilled)
                .nextAll('label').css('color', this.options.colorFilled);
            checkedInputs.prevAll('label').css('color', this.options.colorUnfilled)
        }
    });
})(jQuery);
