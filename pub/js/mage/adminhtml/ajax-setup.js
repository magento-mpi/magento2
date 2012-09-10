/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function($) {
    $.ajaxSetup({
        /*
         * @type {string}
         */
        type: 'POST',
        /*
         * Ajax before send callback
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {Object}
         */
        beforeSend: function(jqXHR, settings){
            if (!settings.url.match(new RegExp('[?&]isAjax=true',''))) {
                settings.url = settings.url.match(
                    new RegExp('\\?',"g")) ?
                    settings.url + '&isAjax=true' :
                    settings.url + '?isAjax=true';
            }
            if ($.type(settings.data) === "string"
                && settings.data.indexOf('form_key=') == -1
                ) {
                settings.data += '&' + $.param({
                    form_key: FORM_KEY
                });
            } else {
                if (!settings.data) {
                    this.options.data = {
                        form_key: FORM_KEY
                    };
                }
                if (!settings.data.form_key) {
                    settings.data.form_key = FORM_KEY;
                }
            }
            $(this).get(0) instanceof HTMLElement ?
                $(this).trigger('beforeSend.ajax') :
                $('body').trigger('beforeSend.ajax');
        },
        /*
         * Ajax complete callback
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {string}
         */
        complete: function(jqXHR, status){
            if (jqXHR.readyState == 4) {
                try {
                    var jsonObject = jQuery.parseJSON(jqXHR.responseText);
                    if (jsonObject.ajaxExpired && jsonObject.ajaxRedirect) {
                        window.location.replace(jsonObject.ajaxRedirect);
                        throw new SessionError('session expired');
                    }
                } catch (e) {
                    if (e instanceof SessionError) {
                        return;
                    }
                }
            }
            $(this).get(0) instanceof HTMLElement ?
                $(this).trigger('complete.ajax') :
                $('body').trigger('complete.ajax');
        }
    })
})(jQuery);

(function($){
    $.widget("mage.loader", {
        options: {
            icon: '',
            texts: {
                loaderText: 'Please wait...',
                imgAlt: 'Loading...'
            },
            template: '<div class="loading-mask"><p class="loader">'+
                '<img {{if texts.imgAlt}}alt="${texts.imgAlt}"{{/if}} src="${icon}"><br>'+
                '<span>{{if texts.loaderText}}${texts.loaderText}{{/if}}</span></p></div>'
        },
        /**
         * Loader creation
         * @protected
         */
        _create: function () {
            this._render();
            this._bind();
        },
        /**
         * Bind on ajax complete event
         * @protected
         */
        _bind: function(){
            this.element.on('complete.ajax', function(e){
                e.stopImmediatePropagation();
                $(e.target).is('body') ?
                    $(e.target).loader('destroy') :
                    $(e.target).loader('hide');
            });
        },
        /**
         * Show loader
         */
        show: function () {
            this.loader.show();
        },
        /**
         * Hide loader
         */
        hide: function () {
            this.loader.hide();
        },
        /**
         * Render loader
         * @protected
         */
        _render: function () {
            this.loader = $.tmpl(this.options.template, this.options)
                .css(this._getCssObj());
            this.element.is('body') ?
                this.element.prepend(this.loader) :
                this.element.before(this.loader);
        },
        /**
         * Prepare object with css properties for loader
         * @protected
         */
        _getCssObj: function(){
            var isBodyElement = this.element.is('body'),
                width = isBodyElement ? $(window).width() : this.element.outerWidth(),
                height = isBodyElement ? $(window).height() : this.element.outerHeight(),
                position = isBodyElement ? 'fixed' : 'relative';
            return {
                height: height + 'px',
                width: width + 'px',
                position: position,
                'margin-bottom': '-' + height + 'px'
            }
        },
        /**
         * Destroy loader
         */
        destroy: function () {
            this.loader.remove();
            return $.Widget.prototype.destroy.call(this);
        }
    });
    $(document).ready(function(){
        $('body').on('beforeSend.ajax', function(e){
            $(e.target).loader().show();
        });
    })
})(jQuery);