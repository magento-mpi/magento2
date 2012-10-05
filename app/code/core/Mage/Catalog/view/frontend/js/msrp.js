/**
* {license_notice}
*
* @category    design
* @package     base_default
* @copyright   {copyright}
* @license     {license_link}
*/

/*jshint browser:true jquery:true*/
(function ($) {

    var _clickForPrice = {
        helpLink: []
    };
    var _popupCloseData = {
        closeButtonId: ''
    };

    var _helpLinkData = {
        helpText: []
    };

    var _popupCartData = {
        cartData: []
    };

    var _cartData = {
        addToCartData: []
    };

    $(document).ready(function () {
        $.mage.event.trigger("mage.price.helplink", _clickForPrice);
        $.mage.event.trigger("map.popup.close", _popupCloseData);
        $.mage.event.trigger("mage.popup.whatsthislink", _helpLinkData);
        $.mage.event.trigger("map.popup.button", _popupCartData);
        $.mage.event.trigger("product.addtocart.button", _cartData);

        $.each(_clickForPrice.helpLink, function (index, value) {
            $(value.popupId).on('click', function (e) {
                $('#map-popup-heading').text(value.productName);
                $('#map-popup-price').html($(value.realPrice));
                $('#map-popup-msrp').html(value.msrpPrice);

                var width = $('#map-popup').width();
                var offsetX = e.pageX - (width / 2) + "px";
                $('#map-popup').css({left: offsetX, top: e.pageY}).show();
                $('#map-popup-content').show();
                $('#map-popup-text').addClass('map-popup-only-text').show();
                $('#map-popup-text-what-this').hide();
                return false;
            });
        });

        $.each(_helpLinkData.helpText, function (index, value) {
            $(value.helpLinkId).on('click', function (e) {
                var width = $('#map-popup').width();
                var offsetX = e.pageX - (width / 2) + "px";
                $('#map-popup').css({left: offsetX, top: e.pageY}).show();
                $('#map-popup-content').hide();
                $('#map-popup-text').hide();
                $('#map-popup-text-what-this').show();
                return false;
            });
        });

        $(_popupCloseData.closeButtonId).on('click', function () {
            $('#map-popup').hide();
            return false;
        });

        $.each(_popupCartData.cartData, function (index, value) {
            $(value.popupButtonId).on('click', function () {
                $(value.addToCartForm).mage().validate({
                    errorPlacement: function (error, element) {
                        if (element.is(':radio') || element.is(':checkbox')) {
                            element.closest('ul').after(error);
                        } else {
                            element.after(error);
                        }
                    },
                    highlight: function (element) {
                        if ($(element).is(':radio') || $(element).is(':checkbox')) {
                            $(element).closest('ul').addClass('mage-error');
                        } else {
                            return true;
                        }
                    },
                    unhighlight: function (element) {
                        if ($(element).is(':radio') || $(element).is(':checkbox')) {
                            $(element).closest('ul').removeClass('mage-error');
                        } else {
                            return true;
                        }
                    }
                });
                $(value.addToCartForm).submit();
            });
        });

        $.each(_cartData.addToCartData, function (index, value) {
            $(value.addToCartButtonId).on('click', function () {
                $(value.addToCartForm).mage().validate({
                    errorPlacement: function (error, element) {
                        if (element.is(':radio') || element.is(':checkbox')) {
                            element.closest('ul').after(error);
                        } else {
                            element.after(error);
                        }
                    },
                    highlight: function (element) {
                        if ($(element).is(':radio') || $(element).is(':checkbox')) {
                            $(element).closest('ul').addClass('mage-error');
                        } else {
                            return true;
                        }
                    },
                    unhighlight: function (element) {
                        if ($(element).is(':radio') || $(element).is(':checkbox')) {
                            $(element).closest('ul').removeClass('mage-error');
                        } else {
                            return true;
                        }
                    }
                });
                $(value.addToCartForm).submit();
            });
        });

    });

})(jQuery);

