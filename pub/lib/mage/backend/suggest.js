/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/

(function($) {
    'use strict';
    $.widget('mage.suggest', {
        options: {
            template: '#menuTemplate',
            minLength: 1,
            source: 'http://testsuggest.lo/suggest.php',
            events: {},
            appendTo: 'after',
            control: ':ui-menu'
        },

        /**
         *
         * @private
         */
        _create: function() {
            this._setTemplate();
            this.container = $('<div/>');
            this.element[this.options.appendTo](this.container);
            this._bind();
        },

        /**
         * Return actual value of an "input"-element
         * @return {string}
         * @private
         */
        _value: function() {
            return this.element[this.element.is(':input') ? 'val' : 'text']();
        },

        /**
         * Pass original event to a control component for handling it as it's own event
         * @param {Object} event
         * @private
         */
        _proxyEvents: function(event) {
            this.container
                .find(this.options.control)
                .triggerHandler(event);
        },

        /**
         *
         * @param event
         * @private
         */
        _submitAction: function(event) {
            event.preventDefault();
        },

        /**
         *
         * @private
         */
        _bind: function() {
            this._on($.extend({
                'keydown': function(event) {
                    var keyCode = $.ui.keyCode;
                    switch (event.keyCode) {
                        case keyCode.HOME:
                        case keyCode.END:
                        case keyCode.PAGE_UP:
                        case keyCode.PAGE_DOWN:
                        case keyCode.ESCAPE:
                        case keyCode.UP:
                        case keyCode.DOWN:
                        case keyCode.LEFT:
                        case keyCode.RIGHT:
                            this._proxyEvents(event);
                            break;
                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                            if(this.container.is(':visible')) {
                                this._proxyEvents(event);
                            } else {
                                this._submitAction(event);
                            }
                            break;
                        default:
                            this.search();
                    }
                }
            }, this.options.events));
            this._on(this.container, {
                menufocus: function(e, ui) {
                    this.element.val(ui.item.text());
                }
            });
        },

        /**
         *
         * @private
         */
        _setTemplate: function() {
            this.template = $(this.options.template).length ?
                $(this.options.template).template() :
                $.template('suggestTemplate', this.options.template);
        },

        /**
         *
         * @return {*}
         */
        search: function() {
            var val = this._value();
            if(this.term !== val && val.length) {
                this.term = val;
                return this._search(val);
            }
        },

        /**
         *
         * @param value
         * @private
         */
        _search: function(value) {
            this.element.addClass('ui-autocomplete-loading');
            this.cancelSearch = false;
            this._source(value, $.proxy(this._renderData, this));
        },

        /**
         *
         * @param data
         * @private
         */
        _renderData: function(data) {
            if(this.suggestElement) {
                this.suggestElement.remove();
            }

            this.suggestElement = $.tmpl(this.template, {data: data}).appendTo(this.container);
            this.suggestElement.trigger('contentUpdated');
        },

        /**
         *
         * @param value
         * @param render
         * @private
         */
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

        /**
         *
         * @param data
         * @param term
         * @return {*}
         */
        filter: function(data, term) {
            var matcher = new RegExp(term, 'i');
            return $.grep(data, function(value) {
                return matcher.test(value.label || value.value || value);
            });
        }
    });

    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            delay: 500
        },

        /**
         *
         * @private
         */
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

    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            showRecent: true,
            storageKey: 'suggest'
        },

        /**
         *
         * @private
         */
        _create: function() {
            if (this.options.showRecent && window.localStorage) {
                var data = localStorage.getItem(this.options.storageKey);
                this.recentData = JSON.parse(data) || [];
            }
            this._super();
        },

        /**
         *
         * @private
         */
        _bind: function() {
            this._super();
            this._on({
                focus: function() {
                    if (!this._value()) {
                        this._renderData(this.recentData);
                    }
                }
            });
            this._on(this.container, {
                menuselect: function(e, ui) {
                    this._addRecent(ui.item.text());
                }
            });
        },

        /**
         *
         * @param val
         * @private
         */
        _addRecent: function(val) {
            this.recentData.push({label: val, value:val});
            //localStorage.setItem(this.options.storageKey, null);
            localStorage.setItem(this.options.storageKey, JSON.stringify(this.recentData));
        }

    });
})(jQuery);
