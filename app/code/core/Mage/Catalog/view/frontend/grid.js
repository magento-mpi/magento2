/**
 * {license_notice}
 *
 * @category    frontend grid
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global mage:true */
(function ($) {
    // Default fields to initialize for grid
    var gridInit = {
        listId: null,
        decoratorParams: null,
        genericSelector: null
    };

    $(document).ready(function () {
        // Trigger initalize event
        mage.event.trigger("mage.grid.initialize", gridInit);

        if (gridInit.listId) {
            jQuery.mage.decorator.list(gridInit.listId);
        }
        if (gridInit.genericSelector) {
            if (gridInit.decoratorParams) {
                jQuery.mage.decorator.general($(gridInit.genericSelector), gridInit.decoratorParams);
            }
            else {
                jQuery.mage.decorator.general($(gridInit.genericSelector));
            }
        }
    });
}(jQuery));