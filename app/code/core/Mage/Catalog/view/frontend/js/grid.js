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
    var gridInit = {
        listId: undefined,
        decoratorParams: undefined,
        genericSelector: undefined
    };

    $(document).ready(function () {
        $.mage.event.trigger("mage.grid.initialize", gridInit);
        if (gridInit.listId) {
            $(gridInit.listId).decorate('list');
        }
        if (gridInit.genericSelector) {
            if (gridInit.decoratorParams) {
                $(gridInit.genericSelector).decorate('generic', gridInit.decoratorParams);
            }
            else {
                $(gridInit.genericSelector).decorate('generic');
            }
        }
    });
})(jQuery);