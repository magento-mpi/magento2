/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.pbridge', {
        options : {
            method: '',
            frameUrl: '',
            iframeContainerSelector: '[data-container="iframe"]',
            bodySelector: '[data-container="body"]',
            pmtsBtnsContainerSelector: '#payment-buttons-container',
            reloadSelector: '.pbridge-reload'
        },

        _create : function() {
            this.element
                .on('reloadPbridgeIframe', $.proxy(function(event, data) { this.reloadIframe(data); }, this))
                .find('span.pbridge-reload a').on('click', $.proxy(function () {
                    var data = {};
                    data.method = this.options.method;
                    data.frameUrl = this.options.frameUrl;
                    this._reloadIframe(data);

                    return false;
                }, this));
            this.element.closest('[data-container="body"]').on('reloadIframe', $.proxy(this._reloadIframe,this));
        },

        /**
         *
         * @param data
         */
        _reloadIframe: function(data) {
            if (!data) {
                data = {
                    method: this.options.method,
                    frameUrl: this.options.frameUrl
                };
            }

            var hiddenElms = this.element.find('input:hidden');
            if (hiddenElms.length > 0) {
                hiddenElms.remove();
            }

            $.ajax({
                url: data.frameUrl,
                type: 'post',
                context: this,
                data:{method_code: data.method},
                success: function(response) {
                    this.element.find(this.options.iframeContainerSelector).html(response);
                    this.element.trigger('gotoSection', 'payment').trigger('contentUpdate');
                    this.element.find(this.options.reloadSelector).find('a').show();
                }
            });
        }
    });
})(jQuery);
