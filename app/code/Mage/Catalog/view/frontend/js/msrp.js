/**
 * {license_notice}
 *
 * @category    frontend product msrp
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint evil:true browser:true jquery:true*/
(function($) {
    $.widget('mage.addToCart', {
        options: {
            groupedProductContainer: '.grouped-items-table',
            showAddToCart: true
        },

        _create: function() {
            $(this.options.cartButtonId).on('click', $.proxy(function() {
                this._addToCartSubmit();
            }, this));

            $(this.options.popupId).on('click', $.proxy(function(e) {
                if (this.options.submitUrl) {
                    location.href = this.options.submitUrl;
                } else {
                    $(this.options.popupCartButtonId).off('click');
                    $(this.options.popupCartButtonId).on('click', $.proxy(function() {
                        this._addToCartSubmit();
                    }, this));
                    $('#map-popup-heading').text(this.options.productName);
                    $('#map-popup-price').html($(this.options.realPrice));
                    $('#map-popup-msrp').html(this.options.msrpPrice);
                    this.element.trigger('reloadPrice');
                    var width = $('#map-popup').width();
                    var offsetX = e.pageX - (width / 2) + "px";
                    $('#map-popup').css({left: offsetX, top: e.pageY}).show();
                    if (!this.options.showAddToCart) {
                        $('#map-popup-content > .map-popup-checkout').hide();
                    }
                    $('#map-popup-content').show();
                    $('#map-popup-text').addClass('map-popup-only-text').show();
                    $('#map-popup-text-what-this').hide();
                    return false;
                }
            }, this));

            $(this.options.helpLinkId).on('click', $.proxy(function(e) {
                $('#map-popup-heading').text(this.options.productName);
                var width = $('#map-popup').width();
                var offsetX = e.pageX - (width / 2) + "px";
                $('#map-popup').css({left: offsetX, top: e.pageY}).show();
                $('#map-popup-content').hide();
                $('#map-popup-text').hide();
                $('#map-popup-text-what-this').show();
                return false;
            }, this));

            $(this.options.closeButtonId).on('click', $.proxy(function() {
                $('#map-popup').hide();
                return false;
            }, this));

        },

        _addToCartSubmit: function() {
            this.element.trigger('addToCart', this.element);
            if (this.options.addToCartUrl) {
                $('#map-popup').hide();
                if (opener !== null) {
                    opener.location.href = this.options.addToCartUrl;
                } else {
                    location.href = this.options.addToCartUrl;
                }

            } else if (this.options.cartForm) {
                $(this.options.cartForm).submit();
            }
        }
    });
})(jQuery);

