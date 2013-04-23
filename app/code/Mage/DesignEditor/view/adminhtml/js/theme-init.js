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
            registerElementsEvent : 'registerElements'
        },

        /**
         * Initialize widget
         *
         * @protected
         */
        _create: function() {
            if (this.options.isPhysicalTheme) {
                this._bind();
            }
        },

        /**
         * Bind event handlers
         *
         * @protected
         */
        _bind: function() {
            var body = $('body');
            body.on(this.options.registerElementsEvent, $.proxy(this._onRegisterElements, this));
        },

        /**
         * Event handler
         *
         * @param e
         * @param data
         * @protected
         */
        _onRegisterElements: function(e, data){
            var content = data.content ? $(data.content).contents() : $('body');
            this._registerElements(content, data.elements);
        },

        /**
         * Register elements
         *
         * @param content
         * @param elements
         * @protected
         */
        _registerElements: function(content, selectorsByEvent) {
            console.log(selectorsByEvent);
            for (var eventType in selectorsByEvent) {
                for (var i = 0; i < selectorsByEvent[eventType].length; i++){
                    var selector = selectorsByEvent[eventType][i];
                    content.find(selector).on(eventType, $.proxy(this._onChangeTheme, this));
                }
            }
        },

        /**
         * Manage change theme event
         *
         * @param event
         * @protected
         */
        _onChangeTheme: function(event) {
            console.log('change');
            if (confirm($.mage.__('You want to change theme. It is necessary to create customization. Do you want to create?'))) {
                this._createVirtualTheme();
            }
            event.stopPropagation();
            $(event.target).blur();
            return false;
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
