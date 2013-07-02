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
            this._render();
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
                'ajaxComplete': '_ajaxJobDone',
                'ajaxError': '_ajaxJobDone',
                'processStop': '_ajaxJobDone',
                'processStart': 'show',
                'ajaxStart': 'show',
                'show.loader': 'show',
                'hide.loader': 'hide',
                'contentUpdated.loader': '_contentUpdated'
            });
        },

        /**
         * Stop the propagation of the event and hide the loader. Used for ajaxComplete, ajaxError, and processStop
         * events. It will call stopImmediatePropagation on the event and then hide the loader.
         *
         * @param event
         * @private
         */
        _ajaxJobDone: function(event) {
            event.stopImmediatePropagation();
            this.hide();
        },

        /**
         * Verify loader present after content updated
         *
         * @param event
         * @private
         */
        _contentUpdated: function(event) {
            if (!this.element.find('[data-role="loader"]').length) {
                this._render();
            }
        },

        /**
         * Show loader
         */
        show: function() {
            if (!this.element.find('[data-role="loader"]').length) {
                this._render();
            }
            this.loader.show();
        },

        /**
         * Hide loader
         */
        hide: function() {
            if (this.loader) {
                this.loader.hide();
            }
        },

        /**
         * Render loader
         * @protected
         */
        _render: function() {
            this.loader = $.tmpl(this.options.template, this.options)
                .css(this._getCssObj());

            this.element.prepend(this.loader);
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
            this.loader.remove();
            // bindings are automatically removed by jquery since we used the _on method to register them
            this._super();
        }
    });
})(jQuery);
