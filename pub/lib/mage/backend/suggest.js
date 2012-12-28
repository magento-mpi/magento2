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
    /**
     * Implement base functionality
     */
    $.widget('mage.suggest', {
        options: {
            template: '#menuTemplate',
            minLength: 1,
            /**
             * @type {(string|Array)}
             */
            source: 'http://testsuggest.lo/suggest.php',
            events: {},
            appendTo: 'after',
            control: ':ui-menu'
        },

        /**
         * Component's constructor
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
         * Handle submit action
         * @param event
         * @private
         */
        _submitAction: function(event) {
            event.preventDefault();
        },

        /**
         * Bind handlers on specific events
         * @private
         */
        _bind: function() {
            this._on($.extend({
                keydown: function(event) {
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
                            if (this.container.is(':visible')) {
                                this._proxyEvents(event);
                                this.container.empty().hide();
                            }
                            break;
                    }
                },
                keyup: function(event) {
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
                            break;
                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                            if (this.container.is(':hidden')) {
                                this._submitAction(event);
                            }
                            break;
                        default:
                            if (this.container.is(':hidden')) {
                                this.container.show();
                            }
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
         * Acquire content template
         * @private
         */
        _setTemplate: function() {
            this.template = $(this.options.template).length ?
                $(this.options.template).template() :
                $.template('suggestTemplate', this.options.template);
        },

        /**
         * Execute search process
         * @public
         */
        search: function() {
            var term = this._value();
            if (this.term !== term && term.length) {
                this.term = term;
                this._search(term);
            }
        },

        /**
         * Actual search method, can be overridden in descendants
         * @param {string} term - search phrase
         * @private
         */
        _search: function(term) {
            this.element.addClass('ui-autocomplete-loading');
//            this.cancelSearch = false; // have not found usages, so commented it
            this._source(term, $.proxy(this._renderData, this));
        },

        /**
         * Render content of suggest's dropdown
         * @param {Object} data
         * @private
         */
        _renderData: function(data) {
            $.tmpl(this.template, {data: data}).appendTo(this.container.empty());
            this.container.trigger('contentUpdated');
        },

        /**
         * Implement search process via spesific source
         * @param {string} term - search phrase
         * @param {Function} render - search results handler, display search result
         * @private
         */
        _source: function(term, render) {
            if ($.isArray(this.options.source)) {
                render(this.filter(this.options.source, term));

            } else if ($.type(this.options.source) === 'string') {
                $.ajax($.extend({
                    url: this.options.source,
                    type: 'POST',
                    dataType: 'json',
                    data: {q: term},
                    success: render,
                    showLoader: true
                }, this.options.ajaxOptions || {}));
            }
        },

        /**
         * Perform filtering in advance loaded data and returns search result
         * @param {Array} data - all available data
         * @param {string} term - search phrase
         * @return {Object}
         */
        filter: function(data, term) {
            var matcher = new RegExp(term, 'i');
            return $.grep(data, function(value) {
                return matcher.test(value.label || value.value || value);
            });
        }
    });

    /**
     * Implement search delay option
     */
    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            delay: 500
        },

        /**
         * @override
         * @private
         */
        _search: function() {
            var args = arguments;
            if (this.options.delay) {
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

    /**
     * Implement storing search history and display recent searches
     */
    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            showRecent: true,
            storageKey: 'suggest',
            storageLimit: 10
        },

        /**
         * @override
         * @private
         */
        _create: function() {
            if (this.options.showRecent && window.localStorage) {
                var recentData = JSON.parse(localStorage.getItem(this.options.storageKey));
                this.recentData = $.isArray(recentData) ? recentData : [];
            }
            this._super();
        },

        /**
         * @override
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
                    this._addRecent(ui.item.data('item-data'));
                }
            });
        },

        /**
         *
         * @param val
         * @private
         */
        _addRecent: function(data) {
            this.recentData = $.grep(this.recentData, function(obj){
                return obj.value !== data.value;
            });
            this.recentData.unshift(data);
            this.recentData = this.recentData.slice(0, this.options.storageLimit);
            localStorage.setItem(this.options.storageKey, JSON.stringify(this.recentData));
        }
    });

    /**
     * Implement show all functionality
     */
    $.widget('mage.suggest', $.mage.suggest, {
        _bind: function() {
            this._super();
            this._on(this.container, {
                showAll: function() {
                    this._search('', this._renderData);
                }
            });
        }
    });

})(jQuery);
