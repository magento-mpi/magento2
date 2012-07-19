/**
 * {license_notice}
 *
 * @category    catalogsearch search
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint eqnull:true */
(function ($) {
    $(document).ready(function () {
        // Trigger initalize event
        var searchInit = {};
        //defaultValues
        searchInit.minSrchKeyLen = 2;
        searchInit.responseFieldElements = 'ul li';
        mage.event.trigger('mage.catalogsearch.initialize', searchInit);

        $(searchInit.searchFieldId).on('focusout', function () {
            if ($(this).val() == '') {
                $(this).val(searchInit.emptyText);
            }
            setTimeout(function () {
            }, 1);
            $(searchInit.destinationId).hide();
        });

        $(searchInit.searchFieldId).trigger('focusout');

        $(searchInit.searchFieldId).on('focus', function () {
            if ($(this).val() == searchInit.emptyText) {
                $(this).val('');
            }
        });

        $(searchInit.searchFieldId).on('keyup ', function (e) {
            if (e.which == 27)
                $(searchInit.destinationId).hide();
        });

        $(searchInit.searchFormId).on('submit', function (e) {
            if ($(searchInit.searchFieldId).val() == searchInit.emptyText || searchInit.searchFieldId == '') {
                e.preventDefault();
            }

        });

        $(searchInit.searchFieldId).on('input propertychange', function () {
            if ($(this).val().length >= parseInt(searchInit.minSrchKeyLen)) {
                $.get(searchInit.url, {q: $(this).val()}, function (data) {
                    $(searchInit.destinationId).html(data)
                        .css('position', 'absolute')
                        .width($(searchInit.searchFieldId).outerWidth())
                        .show()
                        .find(searchInit.responseFieldElements).on('click', function () {
                            $(searchInit.searchFieldId).val($(this).attr('title'));
                            $(searchInit.searchFormId).trigger('submit');
                        });
                })
            } else {
                $(searchInit.destinationId).hide();
            }
        });

    });
}(jQuery));

