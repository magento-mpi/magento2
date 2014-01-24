/**
 * Handles additional ajax request for rendering user private content
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function ($) {
    "use strict";
    $.widget('mage.pageCache', {
        options: {
            url: '/',
            dataAttributeName: 'fpc',
            versionCookieName: 'private_content_version'
        },
        selector: null,
        _create: function () {
            var version = $.mage.cookies.get(this.options.versionCookieName) || '';
            if (!version) {
                return ;
            }
            this.selector = '*[data-' + this.options.dataAttributeName + ']';
            this._ajax(this.element.find(this.selector), version);
        },
        _ajax: function (elements, version) {
            var data = {
                block: [],
                version: version
            };
            for (var i = 0; i < elements.length; i++) {
                var fpc = $(elements[i]).data(this.options.dataAttributeName);
                data.block.push(fpc);
            }
            if (!data) {
                return;
            }
            $.ajax({
                url: this.options.url,
                data: data,
                type: 'GET',
                cache: true,
                dataType: 'html',
                context: this,
                success: function (data) {
                    var elements = $(data).find(this.selector);
                    for (var i = 0; i < elements.length; i++) {
                        var $el = $(elements[i]),
                            attrName = 'data-' + this.options.dataAttributeName,
                            attr = $el.attr(attrName),
                            selector = '*[' + attrName + '="' + attr + '"]';
                        this.element.find(selector).html($el.html());
                    }
                }
            });
        }
    });
})(jQuery);