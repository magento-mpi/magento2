/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/

(function($) {
    $.extend(true, $, {
        mage: {
            isStorageAvailable: function() {
                try {
                    return 'localStorage' in window && window['localStorage'] !== null;
                } catch (e) {
                    return false;
                }
            }
        }
    });

    'use strict';
    $.widget('mage.suggest', {
        options: {
            template: '#menuTemplate',
            minLength: 1,
            source: 'http://testsuggest.lo/suggest.php'
        },
        _create: function() {
            this._setTemplate();
            this.contentContainer = $('<div></div>');
            this.element.after(this.contentContainer);
            this._bind();
        },
        _value: function() {
            return this.element[this.element.is(":input") ? "val" : "text"]();
        },
        _bind: function() {
            this._on($.extend({
                keydown: function(event) {
                    var keyCode = $.ui.keyCode;
                    switch (event.keyCode) {
                        case keyCode.UP:
                            this.contentContainer
                                .find(this.options.contentType)
                                .triggerHandler(event);
                            break;
                        case keyCode.DOWN:
                            this.contentContainer
                                .find(this.options.contentType)
                                .triggerHandler(event);
                            break;
                    }
                },
                keyup: this.search
            }, this.options.events || {}));
            this.contentContainer.on('menufocus', $.proxy(function(e, ui){
                this.element.val(ui.item.text());
            }, this))
        },
        _setTemplate: function() {
            this.template = $(this.options.template).length ?
                $(this.options.template).template() :
                $.template("suggestTemplate", this.options.template);
        },
        search: function() {
            var val = this._value();
            if(this.term !== val && val.length) {
                this.term = val;
                return this._search(val);
            }
        },
        _search: function(value) {
            this.element.addClass( "ui-autocomplete-loading" );
            this.cancelSearch = false;
            this._source(value, $.proxy(this._renderData, this));
        },
        _renderData: function(data) {
            if(this.suggestElement) {
                this.suggestElement.remove();
            }

            this.suggestElement = $.tmpl(this.template, {data: data}).appendTo(this.contentContainer);
            this.suggestElement.trigger('contentUpdated');
        },
        _source: function(value, render) {
            if ($.isArray(this.options.source)) {
                render(this.filter(this.options.source, value));
            } else if($.type(this.options.source) === 'string') {
                $.ajax($.extend({
                    url: this.options.source,
                    type: 'POST',
                    dataType: 'json',
                    data: {q: value},
                    success: render,
                    showLoader: true
                }, this.options.ajaxOptions || {}));
            }
        },
        filter: function(data, term) {
            var matcher = new RegExp(term, "i");
            return $.grep(data, function(value) {
                return matcher.test(value.label || value.value || value);
            });
        }
    });

    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            delay: 500
        },
        _search: function() {
            var args = arguments;
            if(this.options.delay) {
                clearTimeout(this.searchTimeout);
                var _super = $.proxy(this._superApply, this);
                this.searchTimeout = this._delay(function() {
                    _super(args);
                }, this.options.delay);
            } else {
                this._superApply(args);
            }
        }
    });

    /*$.widget('mage.suggest', $.mage.suggest, {
     options: {
     showRecent: true
     },
     _create: function()
     });*/
})(jQuery);