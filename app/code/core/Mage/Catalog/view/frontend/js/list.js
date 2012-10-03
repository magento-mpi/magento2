/**
 * {license_notice}
 *
 * @category    mage compare list
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/

(function ($) {
    $(document).ready(function () {
        var _compareList = {
            productSelector: null,
            productImageSelector: null,
            productAddToCartSelector: null,
            productWishListSelector: null,
            productRemoveSelector: null,
            productFormSelector: null,
            ajaxSpinner: null,
            windowCloseSelector: null,
            printSelector: null
        };

        $.mage.event.trigger('mage.compare-list.initialize', _compareList);
        $.mage.decorator.table(_compareList.productFormSelector);

        function _setParentWindow(selector) {
            $(selector).on('click', function (e) {
                e.preventDefault();
                window.opener.focus();
                window.opener.location.href = $(this).data('url');
            });
        }

        // Window close
        $(_compareList.windowCloseSelector).on('click', function () {
            window.close();
        });
        // Window print
        $(_compareList.printSelector).on('click', function (e) {
            e.preventDefault();
            window.print();
        });

        $(_compareList.productRemoveSelector).on('click', function (e) {
            e.preventDefault();
            // Send remove item request, after that reload windows
            $.ajax({
                url: $(_compareList.productRemoveSelector).data('url'),
                type: 'POST',
                beforeSend: function () {
                    $(_compareList.ajaxSpinner).show();
                }
            }).done(function () {
                $(_compareList.ajaxSpinner).hide();
                window.location.reload();
                window.opener.location.reload();
            });
        });

        $.each(_compareList, function (index, prop) {
            // Removed properties that doesn't need to call _setParentWindow
            var notAllowedProp = ['windowCloseSelector', 'printSelector', 'productRemoveSelector', 'ajaxSpinner','productFormSelector'];
            if ($.inArray(index, notAllowedProp) === -1) {
                _setParentWindow(prop);
            }
        });
    });
})(jQuery);