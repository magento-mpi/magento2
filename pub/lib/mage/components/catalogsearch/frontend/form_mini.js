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

        var searchInit = {
            // Default values
            minSearchLength: 2,
            responseFieldElements: 'ul li',
            selectClass: 'selected',
            // Filled in initialization event
            emptyText: null,
            destinationId: null,
            searchFieldId: null,
            searchFormId: null
        };
        // Trigger initialize event
        mage.event.trigger('mage.catalogsearch.initialize', searchInit);

        var responseList = {
            indexList: null,
            selected: null
        };

        function getFirstElement() {
            if (responseList.indexList) {
                return  responseList.indexList.first().is(':visible')  ? responseList.indexList.first() : responseList.indexList.first().next();
            }
            return false;
        };

        function getLastElement() {
            if (responseList.indexList) {
                return responseList.indexList.last();
            }
            return false;
        };

        function resetResponseList(all) {
            responseList.selected = null;
            if (all === true) {
                responseList.indexList = null;
            }
        }

        $(searchInit.searchFieldId).on('blur', function () {
            if ($(this).val() === '') {
                $(this).val(searchInit.emptyText);
            }
            // needed to make click events working
            setTimeout(function () {
                $(searchInit.destinationId).hide();
            }, 250);
        });

        $(searchInit.searchFieldId).trigger('blur');

        $(searchInit.searchFieldId).on('focus', function () {
            if ($(this).val() == searchInit.emptyText) {
                $(this).val('');
            }
        });

        $(searchInit.searchFieldId).on('keydown', function (e) {
            var keyCode = e.keyCode || e.which;

            switch (keyCode) {

                case mage.constant.KEY_ESC:
                    resetResponseList(true);
                    $(searchInit.destinationId).hide();
                    break;
                case mage.constant.KEY_TAB:
                    $(searchInit.searchFormId).trigger('submit');
                    break;
                case mage.constant.KEY_DOWN:
                    if (responseList.indexList) {
                        if (!responseList.selected) {
                            getFirstElement().addClass(searchInit.selectClass);
                            responseList.selected = getFirstElement();
                        }
                        else if (!getLastElement().hasClass(searchInit.selectClass)) {
                            responseList.selected = responseList.selected.removeClass(searchInit.selectClass).next();
                            responseList.selected.addClass(searchInit.selectClass);
                        } else {
                            responseList.selected.removeClass(searchInit.selectClass);
                            getFirstElement().addClass(searchInit.selectClass);
                            responseList.selected = getFirstElement();
                        }

                    }
                    break;
                case mage.constant.KEY_UP:
                    if (responseList.indexList !== null) {
                        if (!getFirstElement().hasClass(searchInit.selectClass)) {
                            responseList.selected = responseList.selected.removeClass(searchInit.selectClass).prev();
                            responseList.selected.addClass(searchInit.selectClass);
                        } else {
                            responseList.selected.removeClass(searchInit.selectClass);
                            getLastElement().addClass(searchInit.selectClass);
                            responseList.selected = getLastElement();
                        }

                    }
                    break;
                default:
                    return true;
            }

        });

        $(searchInit.searchFormId).on('submit', function (e) {
            if ($(searchInit.searchFieldId).val() === searchInit.emptyText || $(searchInit.searchFieldId).val() === '') {
                e.preventDefault();
            }
            if (!responseList.selected) {
                $(searchInit.searchFieldId).val(searchInit.selected.attr('title'));
            }

        });

        $(searchInit.searchFieldId).on('input propertychange', function () {

            var searchField = $(this);
            var clonePostion = {
                position: 'absolute',
                left: searchField.offset().left,
                top: searchField.offset().top + searchField.outerHeight(),
                width: searchField.outerWidth()
            };
            if ($(this).val().length >= parseInt(searchInit.minSearchLength, 10)) {
                $.get(searchInit.url, {q: $(this).val()}, function (data) {
                    responseList.indexList = $(searchInit.destinationId).html(data)
                        .css(clonePostion)
                        .show()
                        .find(searchInit.responseFieldElements);
                    resetResponseList();
                    responseList.indexList.on('click',function () {
                        $(searchInit.searchFormId).trigger('submit');
                    }).on('hover',function () {
                            responseList.indexList.removeClass(searchInit.selectClass);
                            $(this).addClass(searchInit.selectClass);
                            responseList.selected = $(this);
                        }).on('mouseout', function () {
                            if (!getLastElement()&& getLastElement().hasClass(searchInit.selectClass)) {
                                $(this).removeClass(searchInit.selectClass);
                                resetResponseList();
                            }

                        });
                });
            } else {
                resetResponseList(true);
                $(searchInit.destinationId).hide();
            }
        });

    });
}(jQuery));

