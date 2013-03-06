/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function ($) {
    $.widget('vde.vdeThemeInit', {
        options: {
            isPhysicalTheme: 0,
            createVirtualThemeUrl: null,
            changeThemeEvent : 'changeTheme'
        },

        /**
         * Initialize widget
         *
         * @private
         */
        _create: function() {
            this._bind();
        },

        /**
         * Bind event handlers
         *
         * @private
         */
        _bind: function() {
            var body = $('body');
            body.on(this.options.changeThemeEvent, $.proxy(this._onChangeTheme, this));
        },

        /**
         * Manage change theme event
         *
         * @param event
         * @param data
         * @private
         */
        _onChangeTheme: function(event, data)
        {
            if (!this.options.isPhysicalTheme) {
                return true;
            }

            if (confirm($.mage.__('You want to change theme. It is necessary to create customization. Do you want to create?'))) {
                this._createVirtualTheme();
                return true;
            }
            return false;
        },

        /**
         * Create virtual theme
         *
         * @private
         */
        _createVirtualTheme: function()
        {
            alert('Creation virtual theme.');
        }
    });
})(jQuery);
