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
            $(gridInit.listId).decorate('list');
        }
        if (typeof(gridInit.genericSelector) !== undefined) {
            if (typeof(gridInit.decoratorParams) !== undefined) {
                $(gridInit.genericSelector).decorate('generic', gridInit.decoratorParams);
            }
            else {
                $(gridInit.genericSelector).decorate('generic');
            }
        }
    });
})(jQuery);