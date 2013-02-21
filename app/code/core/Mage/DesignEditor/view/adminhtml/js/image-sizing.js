/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    $.widget('vde.vdeImageSizing', {
        options: {
            restoreDefaultDataEvent: 'restoreDefaultData'
        },

        _create: function() {
            this._bind();
        },

        _bind: function() {
            $('body').on(this.options.restoreDefaultDataEvent, $.proxy(this._onRestoreDefaultData, this));
        },

        _onRestoreDefaultData: function(event, data) {
            $("#" + data.location + "_type").val(data.type);
            $("#" + data.location + "_width").val(data.width);
            $("#" + data.location + "_height").val(data.height);
        }
    });
})(jQuery);
