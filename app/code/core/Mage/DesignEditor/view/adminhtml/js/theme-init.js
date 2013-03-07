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
        _onChangeTheme: function(event, data) {
            var data = data || {};
            if (!this.options.isPhysicalTheme) {
                data.doChange = true;
            } else {
                if (confirm($.mage.__('You want to change theme. It is necessary to create customization. Do you want to create?'))) {
                    this._createVirtualTheme();
                }
                data.doChange = false;
                if (typeof data.stopPropagation === 'function') {
                    data.stopPropagation();
                }
            }

            return data.doChange;
        },

        /**
         * Create virtual theme
         *
         * @protected
         */
        _createVirtualTheme: function() {
            $.ajax({
                url: this.options.createVirtualThemeUrl,
                type: "GET",
                dataType: 'JSON',
                success: $.proxy(function (data) {
                    if (!data.error) {
                        this._launchVirtualTheme(data.redirect_url);
                    } else {
                        alert(data.message);
                    }
                }, this),

                error: function(data) {
                    throw Error($.mage.__('Some problem with save action'));
                }
            });
        },

        /**
         * Launch virtual theme
         *
         * @param {String} redirectUrl
         * @protected
         */
        _launchVirtualTheme: function(redirectUrl) {
            window.location.replace(redirectUrl);
        }
    });
})(jQuery);
