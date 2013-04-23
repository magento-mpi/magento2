/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
WebFontConfig = {
    google: { families: [ 'Oswald', 'Droid Serif', 'Berkshire Swash' ] }
};

(function($) {
//    'use strict';

    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
        '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);

    var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = [];

    function setConformingHeight(el, newHeight) {
        // set the height to something new, but remember the original height in case things change
        el.data("originalHeight", (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight")));
        el.height(newHeight);
    }

    function getOriginalHeight(el) {
        // if the height has changed, send the originalHeight
        return (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight"));
    }

    function columnConform() {

        // find the tallest DIV in the row, and set the heights of all of the DIVs to match it.
        $('.products.grid .item.product')
            .add('.widget-viewed-grid .item.product')
            .add('.products.related .item.product')
            .each(function() {

            // "caching"
            var $el = $(this);

            var topPosition = $el.position().top;

            if (currentRowStart != topPosition) {

                // we just came to a new row.  Set all the heights on the completed row
                for(currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) setConformingHeight(rowDivs[currentDiv], currentTallest);

                // set the variables for the new row
                rowDivs.length = 0; // empty the array
                currentRowStart = topPosition;
                currentTallest = getOriginalHeight($el);
                rowDivs.push($el);

            } else {

                // another div on the current row.  Add it to the list and check if it's taller
                rowDivs.push($el);
                currentTallest = (currentTallest < getOriginalHeight($el)) ? (getOriginalHeight($el)) : (currentTallest);

            }
            // do the last row
            for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) setConformingHeight(rowDivs[currentDiv], currentTallest);

        });
    }

    $(window).resize(function() {
        columnConform();
    });

    $(function() {
            columnConform();
    });
})(window.jQuery);
