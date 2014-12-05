/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {

    var userAgent = navigator.userAgent, // user agent identifier
        html = document.documentElement, // html tag
        version = 9, // minimal supported version of IE
        gap = '', // gap between classes
        isIE = false,
        isIE11 = false;

    if (html.className) { // check if neighbour class exist in html tag
        gap = ' ';
    } // end if

    for (version; version <= 10; version++) { // loop from minimal to 10 version of IE
        if (userAgent.indexOf('MSIE ' + version) > -1) { // match IE individual name
            html.className += gap + 'ie' + version;
            isIE = true;
        } // end if
    }

    if (userAgent.match(/Trident.*rv[ :]*11\./)) { // Special case for IE11
        html.className += gap + 'ie11';
        isIE11 = true;
    } // end if

    // sticky footer for IE11-
    if (isIE || isIE11) {
        var pageWrapper = $('.page-wrapper'),
            pageFooter = $('.page-footer'),
            pageWrapperHeight = pageWrapper.outerHeight(true),
            childsHeight = 0,
            heightDifference = 0;

        pageWrapper.children().each(function() {
            childsHeight += $(this).outerHeight(true);
        })

        heightDifference = pageWrapperHeight - childsHeight;

        if (heightDifference > 0) {
            pageFooter.css('margin-top', heightDifference);
        }
    }
})(jQuery);