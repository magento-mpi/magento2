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

    var clickForPrice = {
        helpLink: []
    };
    var popupCloseData = {
        Id: ''
    };

    var helpLinkData = {
        helpText: []
    };

    var popupCartData = {
        Id: ''
    };

    var cartData = {
        Id: ''
    };

    $(document).ready(function () {
        $.mage.event.trigger("mage.price.helplink", clickForPrice);
        $.mage.event.trigger("map.popup.close", popupCloseData);
        $.mage.event.trigger("mage.popup.whatsthislink", helpLinkData);
        $.mage.event.trigger("mage.popup.whatsthislink", helpLinkData);
        $.mage.event.trigger("map.popup.button", popupCartData);
        $.mage.event.trigger("product.addtocart.button", cartData);

        $.each(clickForPrice.helpLink, function (index, value) {
            $(value.Id).on('click', function (e) {
                $('#map-popup-heading').text(value.productName);
                $('#map-popup-price').html($(value.realPrice));
                $('#map-popup-msrp').html(value.msrpPrice);

                var width = $('#map-popup').width();
                var leftVal = e.pageX - (width / 2) + "px";
                $('#map-popup').css({left: leftVal, top: e.pageY}).show();
                $('#map-popup-content').show();
                $('#map-popup-text').addClass('map-popup-only-text').show();
                $('#map-popup-text-what-this').hide();
                return false;
            });
        });

        $.each(helpLinkData.helpText, function (index, value) {
            $(value.Id).on('click', function (e) {
                var width = $('#map-popup').width();
                var leftVal = e.pageX - (width / 2) + "px";
                $('#map-popup').css({left: leftVal, top: e.pageY}).show();
                $('#map-popup-content').hide();
                $('#map-popup-text').hide();
                $('#map-popup-text-what-this').show();
                return false;
            });
        });

        $(popupCloseData.Id).on('click', function () {
            $('#map-popup').hide();
            return false;
        });

        $(popupCartData.Id).on('click', function () {
            $("#product_addtocart_form").submit();
        });

        $(cartData.Id).on('click', function () {

            $("#product_addtocart_form").submit();
        });

    });

})(jQuery);

