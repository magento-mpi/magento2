/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($){
    $.widget("mage.loader", {
        loaderStarted: 0,
        options: {
            icon: '',
            texts: {
                loaderText: $.mage.__('Please wait...'),
                imgAlt: $.mage.__('Loading...')
            },
            template: '<div class="loading-mask" data-role="loader">' +
                         '<div class="loader">'+
                            '<img {{if texts.imgAlt}}alt="${texts.imgAlt}"{{/if}} src="${icon}">'+
                            '<p>{{if texts.loaderText}}${texts.loaderText}{{/if}}</p>' +
                         '</div>' +
                      '</div>'
        },

        /**
         * Loader creation
         * @protected
         */
        _create: function() {
            this._bind();
        },

        /**
         * Loader initialisation
         * @private
         */
        _init: function() {
            if (this.options.showOnInit) {
                this.show();
            }
        },

        /**
         * Bind on ajax events
         * @protected
         */
        _bind: function() {
            this._on({
                'processStop': 'hide',
                'processStart': 'show',
                'show.loader': 'show',
                'hide.loader': 'hide',
                'contentUpdated.loader': '_contentUpdated'
            });
        },

        /**
         * Verify loader present after content updated
         *
         * This will be cleaned up by the task MAGETWO-11070
         *
         * @param event
         * @private
         */
        _contentUpdated: function(event) {
            this.show();
        },

        /**
         * Show loader
         */
        show: function() {
            this._render();
            this.loaderStarted++;
            this.loader.show();
            return false;
        },

        /**
         * Hide loader
         */
        hide: function() {
            if (this.loaderStarted > 0) {
                this.loaderStarted--;
                if (this.loaderStarted === 0) {
                    if (this.loader) {
                        this.loader.hide();
                    }
                }
            }
            return false;
        },

        /**
         * Render loader
         * @protected
         */
        _render: function() {
            if (!this.loader) {
                this.loader = $.tmpl(this.options.template, this.options)
                    .css(this._getCssObj());
                this.element.prepend(this.loader);
            }
        },

        /**
         * Prepare object with css properties for loader
         * @protected
         */
        _getCssObj: function() {
            var isBodyElement = this.element.is('body'),
                width = isBodyElement ? $(window).width() : this.element.outerWidth(),
                height = isBodyElement ? $(window).height() : this.element.outerHeight(),
                position = isBodyElement ? 'fixed' : 'relative';
            return {
                height: height + 'px',
                width: width + 'px',
                position: position,
                'margin-bottom': '-' + height + 'px'
            };
        },

        /**
         * Destroy loader
         */
        _destroy: function() {
            if (this.loader) {
                this.loader.remove();
            }
        }
    });
})(jQuery);
