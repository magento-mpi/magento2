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
            template: '<div class="loading-mask">' +
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
         * Bind on ajax complete event
         * @protected
         */
        _bind: function() {
            this.element.on('ajaxComplete ajaxError processStop', function(e) {
                e.stopImmediatePropagation();
                $($(e.target).is(document) ? 'body' : e.target).loader('hide');
            });
        },

        /**
         * Show loader
         */
        show: function() {
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
        destroy: function() {
            this.loader.remove();
            this.element.off('ajaxComplete ajaxError processStop');
            return $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);
