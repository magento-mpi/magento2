/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";

    $.widget("mage.redirect", {

        options: {
            url: null,
            type: 'assign',     // Type of the redirect:
                                // assign - regular redirect allowing to you to use browser BACK button
                                // replace - same as 'assign' but current page is not saved in browser history
                                // reload - no URL required, reloads current page
            isForced: true,     // for type = 'reload', defines if browser will attempt to load a page from cache
            timeout: 0          //instant redirect (0ms)
        },

        /**
         * Instantly trigger redirect when widget is initialized
         * @private
         */
        _create: function() {
            this.redirect();
        },

        /**
         * Method processes widget options and decides the action which it will take
         *
         * @public
         * @return {Boolean}
         */
        redirect: function() {
            this._presetOptions();
            if (this.options.type === 'reload') {
                if (this.options.timeout > 0) {
                    setTimeout($.proxy(this._processReload, this), this.options.timeout);
                } else {
                    this._processReload();
                }
            } else if (this.options.url) {
                if (this.options.timeout > 0) {
                    setTimeout($.proxy(this._processRedirect, this), this.options.timeout);
                } else {
                    this._processRedirect();
                }
            }
            return false;
        },

        /**
         * Method reloads current page in the browser
         *
         * @protected
         */
        _processReload: function() {
            window.location.reload(this.options.isForced);
        },

        /**
         * Method redirects the user to a specified URL
         *
         * @protected
         */
        _processRedirect: function() {
            if (this.options.type == "assign") {
                window.location.assign(this.options.url);
            } else {
                window.location.replace(this.options.url);
            }

        },

        /**
         * Overrides default options if data attributes are set in the DOM
         *
         * @protected
         */
        _presetOptions: function() {
            this.options.url = (this.element.data('redirect-url')) ?
                this.element.data('redirect-url') :this.options.url;

            this.options.type = (this.element.data('redirect-type')) ?
                this.element.data('redirect-type') :this.options.type;

            this.options.isForced = (this.element.data('redirect-forced')) ?
                this.element.data('redirect-forced') :this.options.isForced;

            this.options.timeout = (this.element.data('redirect-timeout')) ?
                this.element.data('redirect-timeout') :this.options.timeout;
        }
    });

})(jQuery);
