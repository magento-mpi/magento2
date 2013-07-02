/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    $.widget('mage.addressTabs', $.mage.tabs, {
        options: {
        },

        _create: function() {
            this._super();
            this._bind();
        },

        _bind: function() {
        }
    });

    $(document).ready(function() {
        $("#address-tabs").mage('addressTabs');
    });
})(jQuery);