/**
 * {license_notice}
 *
 * @category    frontend grid
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    // No default value because we want to get it from user
    var gridInit = {};

    $(document).ready(function () {
        // Trigger initalize event
        mage.event.trigger("mage.grid.initialize", gridInit);

        //
        if (gridInit.listId) {
            mage.decorator.list(gridInit.listId);
        }
        if (gridInit.genericSelector) {
            if (gridInit.decoratorParam) {
                mage.decorator.general($(gridInit.genericSelector), gridInit.decoratorParam);
            }
            else {
                mage.decorator.general($(gridInit.genericSelector));
            }
        }
    });
}(jQuery));