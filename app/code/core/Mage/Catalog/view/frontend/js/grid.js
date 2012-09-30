/**
 * {license_notice}
 *
 * @category    frontend grid
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    // Default fields to initialize for grid
    var gridInit = {
        listId: undefined,
        decoratorParams: undefined,
        genericSelector: undefined
    };

    $(document).ready(function () {
        $.mage.event.trigger("mage.grid.initialize", gridInit);

        if (typeof(gridInit.listId) !== undefined) {
            $.mage.decorator.list(gridInit.listId);
        }
        if (typeof(gridInit.genericSelector) !== undefined) {
            if (typeof(gridInit.decoratorParams) !== undefined) {
                $.mage.decorator.general($(gridInit.genericSelector), gridInit.decoratorParams);
            }
            else {
                $.mage.decorator.general($(gridInit.genericSelector));
            }
        }
    });
})(jQuery);