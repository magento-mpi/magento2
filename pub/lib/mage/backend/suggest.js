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
        widgetEventPrefix: "suggest",
        options: {
            template: '{{if items.length}}{{if !term && !$data.allShown() && $data.recentShown()}}' +
                '<h5 class="title">${recentTitle}</h5>' +
                '{{/if}}' +
                '<ul data-mage-init="{&quot;menu&quot;:[]}">' +
                '{{each items}}' +
                '{{if !$data.itemSelected($value)}}<li {{html optionData($value)}}>' +
                '<a href="#">${$value.label}</a></li>{{/if}}' +
                '{{/each}}' +
                '{{if !term && !$data.allShown() && $data.recentShown()}}' +
                '<li data-mage-init="{actionLink:{event:&quot;showAll&quot;}}" class="show-all">' +
                '<a href="#">${showAllTitle}</a></li>' +
                '{{/if}}' +
                '</ul>{{else}}<span class="mage-suggest-no-records">${noRecordsText}</span>{{/if}}',
            minLength: 1,
            /**
             * @type {(string|Array)}
             */
            source: null,
            delay: 500,
            loadingClass: 'mage-suggest-state-loading',
            events: {},
            appendMethod: 'after',
            controls: {
                selector: ':ui-menu',
                eventsMap: {
                    focus: ['menufocus'],
                    blur: ['menublur'],
                    select: ['menuselect']
                }
            },
            termAjaxArgument: 'name_part',
            className: null,
            inputWrapper:'<div class="mage-suggest"><div class="mage-suggest-inner"></div></div>',
            dropdownWrapper: '<div class="mage-suggest-dropdown"></div>',
            currentlySelected: null
        },

        /**
         * Component's constructor
         * @private
         */
        _create: function() {
            this._term = '';
            this._nonSelectedItem = {id: '', label: ''};
            this._renderedContext = null;
            this._selectedItem = this._nonSelectedItem;
            this._control = this.options.controls || {};
            this._setTemplate();
            this._prepareValueField();
            this._render();
            this._bind();
        },

        /**
         * Render base elemments for suggest component
         * @private
         */
        _render: function() {
            this.dropdown = $(this.options.dropdownWrapper).hide();
            var wrapper = this.options.className ?
                $(this.options.inputWrapper).addClass(this.options.className) :
                $(this.options.inputWrapper);
            this.element
                .wrap(wrapper)
                [this.options.appendMethod](this.dropdown)
                .attr('autocomplete', 'off');
        },

        /**
         * Define a field for storing item id (find in DOM or create a new one)
         * @private
         */
        _prepareValueField: function() {
            if (this.options.valueField) {
                this.valueField = $(this.options.valueField);
            } else {
                this.valueField = this._createValueField()
                    .insertBefore(this.element)
                    .attr('name', this.element.attr('name'));
                this.element.removeAttr('name');
            }
        },

        /**
         * Create value field which keeps a id for selected option
         * can be overridden in descendants
         * @return {jQuery}
         * @private
         */
        _createValueField: function() {
            return $('<input/>', {
                type: 'hidden'
            });
        },

        /**
         * Component's destructor
         * @private
         */
        _destroy: function() {
            this.element
                .unwrap()
                .removeAttr('autocomplete');
            if (!this.options.valueField) {
                this.element.attr('name', this.valueField.attr('name'));
                this.valueField.remove();
            }
            this.dropdown.remove();
            this._off(this.element, 'keydown keyup blur');
        },

        /**
         * Return actual value of an "input"-element
         * @return {string}
         * @private
         */
        _value: function() {
            return $.trim(this.element[this.element.is(':input') ? 'val' : 'text']());
        },

        /**
         * Pass original event to a control component for handling it as it's own event
         * @param {Object} event - event object
         * @private
         */
        _proxyEvents: function(event) {
            var fakeEvent = $.extend({}, $.Event(event.type), {
                ctrlKey: event.ctrlKey,
                keyCode: event.keyCode,
                which: event.keyCode
            });
            this.dropdown.find(this._control.selector).trigger(fakeEvent);
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
                        case keyCode.PAGE_UP:
                        case keyCode.PAGE_DOWN:
                        case keyCode.UP:
                        case keyCode.DOWN:
                            if (!event.shiftKey) {
                                event.preventDefault();
                                this._proxyEvents(event);
                            }
                            break;
                        case keyCode.TAB:
                            if (this.isDropdownShown()) {
                                this._onSelectItem(event, null);
                                event.preventDefault();
                            }
                            break;
                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                            if (this.isDropdownShown() && this._focused) {
                                this._proxyEvents(event);
                                event.preventDefault();
                            }
                            break;
                        case keyCode.ESCAPE:
                            this.close(event);
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
                            this.search(event);
                    }
                },
                blur: function(event) {
                    this.close(event);
                    this._change(event);
                },
                cut: this.search,
                paste: this.search,
                input: this.search,
                select: this._onSelectItem
            }, this.options.events));

            this._bindDropdown();
        },

        /**
         * @param {Object} e - event object
         * @private
         */
        _change: function(e) {
            if (this._term !== this._value()) {
                this._trigger("change", e);
            }
        },

        /**
         * Bind handlers for dropdown element on specific events
         * @private
         */
        _bindDropdown: function() {
            var events = {
                click: function(e) {
                    // prevent default browser's behavior of changing location by anchor href
                    e.preventDefault();
                },
                mousedown: function(e) {
                    e.preventDefault();
                }
            };
            $.each(this._control.eventsMap, $.proxy(function(suggestEvent, controlEvents) {
                $.each(controlEvents, $.proxy(function(i, handlerName) {
                    switch(suggestEvent) {
                        case 'select' :
                            events[handlerName] = this._onSelectItem;
                            break;
                        case 'focus' :
                            events[handlerName] = this._focusItem;
                            break;
                        case 'blur' :
                            events[handlerName] = this._blurItem;
                            break;
                    }
                }, this));
            }, this));
            this._on(this.dropdown, events);
        },

        /**
         * @override
         */
        _trigger: function(type, event, data) {
            var result = this._superApply(arguments);
            if(result === false && event) {
                event.stopImmediatePropagation();
                event.preventDefault();
            }
            return result;
        },

        /**
         * Handle focus event of options item
         * @param {Object} e - event object
         * @param {Object} option
         * @private
         */
        _focusItem: function(e, option) {
            if(option && option.item) {
                this._focused = $(option.item).prop('tagName') ?
                    this._readItemData(option.item) :
                    option.item;

                this.element.val(this._focused.label);
                this._trigger('focus', e, {item: this._focused});
            }
        },

        /**
         * Handle blur event of options item
         * @private
         */
        _blurItem: function() {
            this._focused = null;
            this.element.val(this._term);
        },

        /**
         * @param {Object} e - event object
         * @param {Object} item
         * @private
         */
        _onSelectItem: function(e, item) {
            if(item && $.type(item) === 'object' && $(e.target).is(this.element)) {
                this._focusItem(e, {item: item});
            }

            if (this._trigger('beforeselect', e || null, {item: this._focused}) === false) {
                return;
            }
            this._selectItem(e);
            this._trigger('select', e || null, {item: this._focused});
        },

        /**
         * Save selected item and hide dropdown
         * @private
         * @param {Object} e - event object
         */
        _selectItem: function(e) {
            if (this._focused) {
                this._selectedItem = this._focused;
                if (this._selectedItem !== this._nonSelectedItem) {
                    this._term = this._selectedItem.label;
                    this.valueField.val(this._selectedItem.id);
                    this.close(e);
                }
            }
        },

        /**
         * Read option data from item element
         * @param {Element} item
         * @return {Object}
         * @private
         */
        _readItemData: function(item) {
            return item.data('suggestOption') || this._nonSelectedItem;
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
         * @param {Object} e - event object
         */
        open: function(e) {
            if (!this.isDropdownShown()) {
                this.dropdown.show();
                this._trigger('open', e);
            }
        },

        /**
         * Close and clear dropdown content
         * @private
         * @param {Object} e - event object
         */
        close: function(e) {
            this.element.val(this._selectedItem.label);
            this._renderedContext = null;
            this.dropdown.hide().empty();
            this._trigger('close', e);
        },

        /**
         * Acquire content template
         * @private
         */
        _setTemplate: function() {
            this.templateName = 'suggest' + Math.random().toString(36).substr(2);
            var template = $(this.options.template);
            if (template.length && template.prop('type')=== 'text/x-jquery-tmpl') {
                template.template(this.templateName);
            } else {
                $.template(this.templateName, this.options.template);
            }
        },

        /**
         * Execute search process
         * @public
         * @param {Object} e - event object
         */
        search: function(e) {
            var term = this._value();
            if (this._term !== term) {
                this._term = term;
                if (term && term.length >= this.options.minLength) {
                    if (this._trigger("search", e) === false) {
                        return;
                    }
                    this._search(e, term, {});
                } else {
                    this._selectedItem = this._nonSelectedItem;
                    this.valueField.val(this._selectedItem.id);
                }
            }
        },

        /**
         * Actual search method, can be overridden in descendants
         * @param {Object} e - event object
         * @param {string} term - search phrase
         * @param {Object} context - search context
         * @private
         */
        _search: function(e, term, context) {
            var response = $.proxy(function(items) {
                return this._processResponse(e, items, context || {});
            }, this);
            this.element.addClass(this.options.loadingClass);
            if (this.options.delay) {
                clearTimeout(this._searchTimeout);
                this._searchTimeout = this._delay(function() {
                    this._source(term, response);
                }, this.options.delay);
            } else {
                this._source(term, response);
            }
        },

        /**
         * Extend basic context with additional data (search results, search term)
         * @param {Object} context
         * @return {Object}
         * @private
         */
        _prepareDropdownContext: function(context) {
            return $.extend(context, {
                items: this._items,
                term: this._term,
                optionData: function(item) {
                    return 'data-suggest-option="' + JSON.stringify(item).replace(/"/g, '&quot;') + '"';
                },
                itemSelected: $.proxy(function(item) {
                    return item.id == (this._selectedItem && this._selectedItem.id ?
                        this._selectedItem.id :
                        this.options.currentlySelected);
                }, this),
                noRecordsText: $.mage.__('No records found')
            });
        },

        /**
         * Render content of suggest's dropdown
         * @param {Object} e - event object
         * @param {Array} items - list of label+id objects
         * @param {Object} context - template's context
         * @private
         */
        _renderDropdown: function(e, items, context) {
            this._items = items;
            $.tmpl(this.templateName, this._prepareDropdownContext(context))
                .appendTo(this.dropdown.empty());
            this.dropdown.trigger('contentUpdated')
                .find(this._control.selector).on('focus', function(e) {
                    e.preventDefault();
                });
            this._renderedContext = context;
            this.element.removeClass(this.options.loadingClass);
            this.open(e);
        },

        /**
         * @param {Object} e
         * @param {Object} items
         * @param {Object} context
         * @private
         */
        _processResponse: function(e, items, context) {
            var renderer = $.proxy(function(items) {
                return this._renderDropdown(e, items, context || {});
            }, this);
            if (this._trigger("response", e, [items, renderer]) === false) {
                return
            }
            this._renderDropdown(e, items, context);
        },

        /**
         * Implement search process via spesific source
         * @param {string} term - search phrase
         * @param {Function} response - search results handler, process search result
         * @private
         */
        _source: function(term, response) {
            var o = this.options;
            if ($.isArray(o.source)) {
                response(this.filter(o.source, term));
            } else if ($.type(o.source) === 'string') {
                if (this._xhr) {
                    this._xhr.abort();
                }
                var ajaxData = {};
                ajaxData[this.options.termAjaxArgument] = term;

                this._xhr = $.ajax($.extend({
                    url: o.source,
                    type: 'POST',
                    dataType: 'json',
                    data: ajaxData,
                    success: response
                }, o.ajaxOptions || {}));
            } else {
                o.source.apply(o.source, arguments);
            }
        },

        /**
         * @private
         */
        _abortSearch: function() {
            this.element.removeClass(this.options.loadingClass);
            clearTimeout(this._searchTimeout);
            if (this._xhr) {
                this._xhr.abort();
            }
        },

        /**
         * Perform filtering in advance loaded items and returns search result
         * @param {Array} items - all available items
         * @param {string} term - search phrase
         * @return {Object}
         */
        filter: function(items, term) {
            var matcher = new RegExp(term.replace(/[\-\/\\\^$*+?.()|\[\]{}]/g, '\\$&'), 'i');
            return $.grep(items, function(value) {
                return matcher.test(value.label || value.id || value);
            });
        }
    });

    /**
     * Implements height prediction functionality to dropdown item
     */
    /*$.widget('mage.suggest', $.mage.suggest, {
        /**
         * Extension specific options
         *//*
        options: {
            bottomMargin: 35
        },

        /**
         * @override
         * @private
         *//*
        _renderDropdown: function() {
            this._superApply(arguments);
            this._recalculateDropdownHeight();
        },

        /**
         * Recalculates height of dropdown and cut it if needed
         * @private
         *//*
        _recalculateDropdownHeight: function() {
            var dropdown = this.dropdown.css('visibility', 'hidden'),
                fromTop = dropdown.offset().top,
                winHeight = $(window).height(),
                isOverflowApplied = (fromTop + dropdown.outerHeight()) > winHeight;

            dropdown
                .css('visibility', '')
                [isOverflowApplied ? 'addClass':'removeClass']('overflow-y')
                .height(isOverflowApplied ? winHeight - fromTop - this.options.bottomMargin : '');
        }
    });*/

    /**
     * Implement show all functionality and storing and display recent searches
     */
    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            showRecent: false,
            showAll: false,
            storageKey: 'suggest',
            storageLimit: 10
        },

        /**
         * @override
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
         */
        _bind: function() {
            this._super();
            this._on(this.dropdown, {
                showAll: function(e) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    this.element.trigger('showAll');
                }
            });
            if (this.options.showRecent || this.options.showAll) {
                this._on({
                    focus: this.search,
                    showAll: this._showAll
                });
            }
        },

        /**
         * @private
         * @param {Object} e - event object
         */
        _showAll: function(e) {
            this._abortSearch();
            this._search(e, '', {_allShown: true});

        },

        /**
         * @override
         */
        search: function(e) {
            if (!this._value()) {
                if (this.options.showRecent) {
                    this._recentItems.length ?
                        this._processResponse(e, this._recentItems, {}) :
                        this._showAll(e);
                } else if (this.options.showAll) {
                    this._showAll(e);
                }
            }
            this._superApply(arguments);
        },

        /**
         * @override
         */
        _selectItem: function() {
            this._superApply(arguments);
            if (this._selectedItem.id && this.options.showRecent) {
                this._addRecent(this._selectedItem);
            }
        },

        /**
         * @override
         */
        _prepareDropdownContext: function() {
            var context = this._superApply(arguments);
            return $.extend(context, {
                recentShown: $.proxy(function(){
                    return this.options.showRecent;
                }, this),
                recentTitle: $.mage.__('Recent items'),
                showAllTitle: $.mage.__('Show all...'),
                allShown: function(){
                    return !!context._allShown;
                }
            });
        },

        /**
         * Add selected item of search result into storage of recents
         * @param {Object} item - label+id object
         * @private
         */
        _addRecent: function(item) {
            this._recentItems = $.grep(this._recentItems, function(obj){
                return obj.id !== item.id;
            });
            this._recentItems.unshift(item);
            this._recentItems = this._recentItems.slice(0, this.options.storageLimit);
            localStorage.setItem(this.options.storageKey, JSON.stringify(this._recentItems));
        }
    });

    /**
     * Implement multi suggest functionality
     */
    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            multiSuggestWrapper: '<ul class="mage-suggest-choices">' +
                '<li class="mage-suggest-search-field"></li></ul>',
            choiceTemplate: '<li class="mage-suggest-choice button"><div>${text}</div>' +
                '<span class="mage-suggest-choice-close" tabindex="-1" ' +
                'data-mage-init="{&quot;actionLink&quot;:{&quot;event&quot;:&quot;removeOption&quot;}}"></span></li>'
        },

        /**
         * @override
         */
        _create: function() {
            this._super();
            this._selectedItems = [];
            if (this.options.multiselect) {
                this.valueField.hide();
            }
        },

        /**
         * @override
         */
        _render: function() {
            this._super();
            if (this.options.multiselect) {
                this.element.wrap(this.options.multiSuggestWrapper);
                this.elementWrapper = this.element.parent();
                this.valueField.find('option').each($.proxy(function(i, option) {
                    option = $(option);
                    this._renderOption({id: option.val(), label: option.text()});
                }, this));
            }
        },
        
        /**
         * @override
         */
        _bind: function() {
            this._super();
            if (this.options.multiselect) {
                this._on({
                    keydown: function(event) {
                        var keyCode = $.ui.keyCode;
                        switch (event.keyCode) {
                            case keyCode.BACKSPACE:
                                if (!this._value()) {
                                    event.preventDefault();
                                    this._removeLastAdded(event);
                                }
                                break;
                        }
                    },
                    removeOption: this.removeOption
                });
            }
        },

        /**
         * @override
         */
        _prepareValueField: function() {
            this._super();
            if (this.options.multiselect && !this.options.valueField && this.options.selectedItems) {
                $.each(this.options.selectedItems, $.proxy(function(i, item) {
                    this._addOption(item);
                }, this));
            }
        },

        /**
         * @override
         */
        _createValueField: function() {
            if (this.options.multiselect) {
                return $('<select/>', {
                    type: 'hidden',
                    multiple: 'multiple'
                });
            } else {
                return this._super();
            }
        },

        /**
         * @override
         */
        _selectItem: function() {
            if (this.options.multiselect) {
                if (this._focused) {
                    this._selectedItem = this._focused;
                    if (this.valueField.find('option[value=' + this._selectedItem.id + ']').length) {
                        this._selectedItem = this._nonSelectedItem;
                    }
                    if (this._selectedItem !== this._nonSelectedItem) {
                        this._term = '';
                        this._addOption(this._selectedItem);
                    }
                }
            } else {
                this._superApply(arguments);
            }
        },

        /**
         * @override
         */
        _prepareDropdownContext: function() {
            var context = this._superApply(arguments);
            if(this.options.multiselect) {
                return $.extend(context, {
                    itemSelected:$.proxy(function(item) {
                        var selected = false;
                        $.each(this._selectedItems, function(i, selectedItem){
                            if(item.id === selectedItem.id) {
                                selected = true;
                                return false;
                            }
                        });
                        return selected;
                    }, this)
                });
            }
            return context;
        },

        /**
         *
         * @param {Object} item
         * @return {Element}
         * @private
         */
        _createOption: function(item) {
            return $('<option value="' + item.id + '" selected="selected">' + item.label + '</option>')
                .data('renderedOption', this._renderOption(item));
        },

        /**
         * Add selected item in to select options
         * @param item
         * @private
         */
        _addOption: function(item) {
            this._selectedItems.push(this._selectedItem);
            this.valueField.append(this._createOption(item));
        },

        /**
         * @param {Object|Element} item
         * @return {Element}
         * @private
         */
        _getOption: function(item){
            return $(item).prop('tagName') ?
                $(item) :
                this.valueField.find('option[value=' + item.id + ']');
        },

        /**
         * Remove last added option
         * @private
         * @param {Object} e - event object
         */
        _removeLastAdded: function(e) {
            var selectedLength = this._selectedItems.length,
                lastAdded = selectedLength ? this._selectedItems[selectedLength - 1] : null;
            if(lastAdded) {
                this.removeOption(e, lastAdded);
            }
        },

        /**
         * Remove item from select options
         * @param {Object} e - event object
         * @param item
         * @private
         */
        removeOption: function(e, item) {
            var option = this._getOption(item);
            this._selectedItems = $.grep(this._selectedItems, function(selectedItem) {
                return selectedItem.id !== item.id;
            });
            option.data('renderedOption').remove();
            option.remove();
        },

        /**
         * Render visual element of selected item
         * @param {Object} item - selected item
         * @private
         */
        _renderOption: function(item) {
            return $.tmpl(this.options.choiceTemplate, {text: item.label})
                .insertBefore(this.elementWrapper)
                .trigger('contentUpdated')
                .on('removeOption', $.proxy(function(e) {
                    this.removeOption(e, item);
                }, this));
        },

        /**
         * @override
         */
        close: function() {
            this._superApply(arguments);
            if (this.options.multiselect) {
                this.element.val('');
            }
        }
    });
})(jQuery);
