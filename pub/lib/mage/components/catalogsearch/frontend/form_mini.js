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
        //default values
        searchInit.minSrchKeyLen = 2;
        searchInit.responseFieldElements = 'ul li';
        searchInit.intervalDuration = 4000;
        searchInit.selectClass = 'selected';
        mage.event.trigger('mage.catalogsearch.initialize', searchInit);
        searchInit.interval = null;
        searchInit.indexList = null;
        searchInit.selected = null;

        var getFirstElement = function () {
            if (indexListNotNull) {
                return  searchInit.indexList.first().is(':visible') === false ? searchInit.indexList.first().next() : searchInit.indexList.first();
            }
            return false;
        };

        var getLastElement = function () {
            if (indexListNotNull) {
                return searchInit.indexList.last();
            }
            return false;
        };

        var indexListNotNull = function () {
            return  (searchInit.indexList == null) ? false : true;
        };

        var resetSearchInit = function (all) {
            searchInit.selected = null;
            if (all === true) {
                searchInit.interval = null;
                searchInit.indexList = null;
            }
        };

        $(searchInit.searchFormId).on("mouseleave",function () {
            searchInit.interval = setTimeout(function () {
                $(searchInit.destinationId).hide();
            }, searchInit.intervalDuration);

        }).on("mouseenter", function () {
                clearTimeout(searchInit.interval);
                $(searchInit.destinationId).show();
            });
        $(searchInit.searchFieldId).on('focusout', function () {
            if ($(this).val() === '') {
                $(this).val(searchInit.emptyText);
            }
        });
        $(searchInit.searchFieldId).trigger('focusout');

        $(searchInit.searchFieldId).on('focus', function () {
            if ($(this).val() == searchInit.emptyText) {
                $(this).val('');
            }
        });

        $(searchInit.searchFieldId).on('keydown', function (e) {
            var keyCode = e.keyCode || e.which;

            switch (keyCode) {

                case mage.constant.KEY_ESC:
                    $(searchInit.destinationId).hide();
                    break;
                case mage.constant.KEY_TAB:
                    $(searchInit.searchFormId).trigger('submit');
                    break;
                case mage.constant.KEY_DOWN:
                    if (indexListNotNull()) {
                        if (searchInit.selected == null) {
                            getFirstElement().addClass(searchInit.selectClass);
                            searchInit.selected = getFirstElement();
                        }
                        else if (!getLastElement().hasClass(searchInit.selectClass)) {
                            searchInit.selected = searchInit.selected.removeClass(searchInit.selectClass).next();
                            searchInit.selected.addClass(searchInit.selectClass);
                        } else {
                            searchInit.selected.removeClass(searchInit.selectClass);
                            getFirstElement().addClass(searchInit.selectClass);
                            searchInit.selected = getFirstElement();
                        }

                    }
                    break;
                case mage.constant.KEY_UP:
                    if (indexListNotNull()) {
                        if (!getFirstElement().hasClass(searchInit.selectClass)) {
                            searchInit.selected = searchInit.selected.removeClass(searchInit.selectClass).prev();
                            searchInit.selected.addClass(searchInit.selectClass);
                        } else {
                            searchInit.selected.removeClass(searchInit.selectClass);
                            getLastElement().addClass(searchInit.selectClass);
                            searchInit.selected = getLastElement();
                        }

                    }
                    break;
                default:
                    return true;
            }

        });

        $(searchInit.searchFormId).on('submit', function (e) {
            if ($(searchInit.searchFieldId).val() === searchInit.emptyText || searchInit.searchFieldId === '') {
                e.preventDefault();
            }
            if (searchInit.selected != null) {
                $(searchInit.searchFieldId).val(searchInit.selected.attr('title'));
            }

        });

        $(searchInit.searchFieldId).on('input propertychange', function () {

            var $this = $(this);
            var clonePostion = {'position': 'absolute',
                'left': $this.offset().left,
                'top': $this.offset().top + $this.outerHeight(),
                'width': $this.outerWidth()
            };
            if ($(this).val().length >= parseInt(searchInit.minSrchKeyLen, 10)) {
                $.get(searchInit.url, {q: $(this).val()}, function (data) {
                    searchInit.indexList = $(searchInit.destinationId).html(data)
                        .css(clonePostion)
                        .show()
                        .find(searchInit.responseFieldElements);
                    resetSearchInit();
                    searchInit.indexList.on('click',function () {
                        $(searchInit.searchFormId).trigger('submit');
                    }).on('hover',function () {
                            searchInit.indexList.removeClass(searchInit.selectClass);
                            $(this).addClass(searchInit.selectClass);
                            searchInit.selected = $(this);
                        }).on('mouseout', function () {
                            if (!getLastElement().hasClass(searchInit.selectClass)) {
                                $(this).removeClass(searchInit.selectClass);
                                resetSearchInit();
                            }

                        });
                });
            } else {
                resetSearchInit(true);
                $(searchInit.destinationId).hide();
            }
        });

    });
}(jQuery));

