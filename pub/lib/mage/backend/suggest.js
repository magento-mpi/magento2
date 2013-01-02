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
            source: null,
            events: {},
            appendMethod: 'after',
            control: ':ui-menu',
            wrapperAttributes: {
                'class': 'mage-suggest'
            },
            attributes: {
                'class': 'mage-suggest-dropdown'
            }
        },

        /**
         * Component's constructor
         * @private
         */
        _create: function() {
            this._setTemplate();
            this.dropdown = $('<div/>', this.options.attributes).hide();
            this.element
                .wrap($('<div/>', this.options.wrapperAttributes))
                .attr('autocomplete', 'off')
                [this.options.appendMethod](this.dropdown);
            this._bind();
        },

        /**
         * Component's destructor
         * @private
         */
        _destroy: function() {
            this.element.removeAttr('autocomplete').unwrap();
            this.dropdown.remove();
            this._off(this.element, 'keydown keyup blur');
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
            this.dropdown
                .find(this.options.control)
                .triggerHandler(event);
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
                        case keyCode.UP:
                        case keyCode.DOWN:
                        case keyCode.LEFT:
                        case keyCode.RIGHT:
                            if (!event.shiftKey) {
                                this._proxyEvents(event);
                            }
                            break;
                        case keyCode.TAB:
                            if (this.isDropdownShown()) {
                                this._enterCurrentValue();
                                event.preventDefault();
                            }
                            break;
                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                            if (this.isDropdownShown()) {
                                this._proxyEvents(event);
                                event.preventDefault();
                            }
                            break;
                        case keyCode.ESCAPE:
                            this._hideDropdown();
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
                            if (this.isDropdownShown()) {
                                event.preventDefault();
                            }
                            break;
                        default:
                            this.search();
                    }
                },
                blur: this._hideDropdown
            }, this.options.events));

            this._on(this.dropdown, {
                menufocus: function(e, ui) {
                    this.element.val(ui.item.text());
                },
                menuselect: this._enterCurrentValue,
                click: this._enterCurrentValue
            });
        },

        /**
         * Save selected item and hide dropdown
         * @private
         */
        _enterCurrentValue: function() {
            if (this.isDropdownShown()) {
                /**
                 * @type {string} - searched phrase
                 * @private
                 */
                this._term = this._value();
                /**
                 * @type {(Object|null)} - label+value object of selected item
                 * @private
                 */
                this._selectedItem = $.grep(this._items, $.proxy(function(v) {
                    return v.label === this._term;
                }, this))[0] || null;
                this._hideDropdown();
            }
        },

        /**
         * Check if dropdown is shown
         * @return {boolean}
         */
        isDropdownShown: function() {
            return this.dropdown.is(':visible');
        },

        /**
         * Open dropdown
         * @private
         */
        _showDropdown: function() {
            if (!this.isDropdownShown()) {
                this.dropdown.show();
            }
        },

        /**
         * Close and clear dropdown content
         * @private
         */
        _hideDropdown: function() {
            this.element.val(this._term);
            this.dropdown.hide().empty();
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
            /**
             * @type {string} - searched phrase
             * @private
             */
            this._term = this._value();
            if (this._term) {
                this._search(this._term);
            }
        },

        /**
         * Actual search method, can be overridden in descendants
         * @param {string} term - search phrase
         * @private
         */
        _search: function(term) {
            this.element.addClass('ui-autocomplete-loading');
            this._source(term, $.proxy(this._renderDropdown, this));
        },

        /**
         * Render content of suggest's dropdown
         * @param {Array} items - list of label+value objects
         * @private
         */
        _renderDropdown: function(items) {
            this._items = items;
            $.tmpl(this.template, {items: items}).appendTo(this.dropdown.empty());
            this.dropdown.trigger('contentUpdated');
            this._showDropdown();
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
         * Perform filtering in advance loaded items and returns search result
         * @param {Array} items - all available items
         * @param {string} term - search phrase
         * @return {Object}
         */
        filter: function(items, term) {
            var matcher = new RegExp(term, 'i');
            return $.grep(items, function(value) {
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
                var recentItems = JSON.parse(localStorage.getItem(this.options.storageKey));
                /**
                 * @type {Array} - list of recently searched items
                 * @private
                 */
                this._recentItems = $.isArray(recentItems) ? recentItems : [];
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
                        this._renderDropdown(this._recentItems);
                    }
                }
            });
        },

        /**
         * @override
         */
        search: function() {
            this._super();
            if (!this._term) {
                this._renderDropdown(this._recentItems);
            }
        },

        /**
         * @override
         * @private
         */
        _enterCurrentValue: function() {
            this._super();
            this._addRecent(this._selectedItem);
        },

        /**
         *
         * @param item
         * @private
         */
        _addRecent: function(item) {
            this._recentItems = $.grep(this._recentItems, function(obj){
                return obj.value !== item.value;
            });
            this._recentItems.unshift(item);
            this._recentItems = this._recentItems.slice(0, this.options.storageLimit);
            localStorage.setItem(this.options.storageKey, JSON.stringify(this._recentItems));
        }
    });

    /**
     * Implement show all functionality
     */
    $.widget('mage.suggest', $.mage.suggest, {
        _bind: function() {
            this._super();
            this._on(this.dropdown, {
                showAll: function() {
                    this._search('', this._renderDropdown);
                }
            });
        }
    });

})(jQuery);
