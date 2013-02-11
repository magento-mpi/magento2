/**
 * {license_notice}
 *
 * @category    frontend product tier price
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint evil:true browser:true jquery:true*/
(function($) {
    $.widget('mage.tierPrice', {
        options: {
            popupHeading: '#map-popup-heading',
            popupPrice: '#map-popup-price',
            popupMsrp: '#map-popup-msrp',
            popup: '#map-popup',
            popupContent: '#map-popup-content',
            popupText: '#map-popup-text',
            popupOnlyText: 'map-popup-only-text',
            popupTextWhatThis: '#map-popup-text-what-this',
            popupCartButtonId: '#map-popup-button'
        },

        _create: function() {
            this.element.on('click', '[data-tier-price]', $.proxy(this._showTierPrice, this));
        },

        /**
         * Show tier price popup on gesture
         * @private
         * @param e - element got the clicked on
         * @return {Boolean}
         */
        _showTierPrice: function(e) {
            var json = eval('(' + $(e.target).data('tier-price') + ')');
            $(this.options.popupCartButtonId).off('click');
            $(this.options.popupCartButtonId).on('click', $.proxy(function() {
                this.element.find(this.options.inputQty).val(json.qty);
                this.element.submit();
            }, this));
            $(this.options.popupHeading).text(json.name);
            $(this.options.popupPrice).html($(json.price)).find('[id^="product-price-"]').attr('id', function() {
                // change price element id, so price option won't update the tier price
                return 'tier' + $(this).attr('id');
            });
            $(this.options.popupMsrp).html(json.msrp);
            var width = $(this.options.popup).width();
            var offsetX = e.pageX - (width / 2) + "px";
            $(this.options.popup).css({left: offsetX, top: e.pageY}).show();
            $(this.options.popupContent).show();
            $(this.options.popupText).addClass(this.options.popupOnlyText).show();
            $(this.options.popupTextWhatThis).hide();
            return false;
        }
    });
})(jQuery);

