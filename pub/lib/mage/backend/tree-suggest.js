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
    var hover_node = $.jstree._instance.prototype.hover_node;
    var dehover_node = $.jstree._instance.prototype.dehover_node;
    var select_node = $.jstree._instance.prototype.select_node;
    var init = $.jstree._instance.prototype.init;
    $.extend(true, $.jstree._instance.prototype, {
        /**
         * @override
         */
        init: function() {
            this.get_container().on('keydown', $.proxy(function(e) {
                if (e.keyCode === $.ui.keyCode.ENTER) {
                    var o = this.data.ui.hovered || this.data.ui.last_selected || -1;
                    this.select_node(o);
                }
            }, this));
            init.call(this);
        },

        /**
         * @override
         */
        hover_node: function(obj) {
            hover_node.apply(this, arguments);
            obj = this._get_node(obj);
            if (!obj.length) {
                return false;
            }
            this.get_container().trigger('hover_node', [{item: obj.find('a:first')}]);
        },

        /**
         * @override
         */
        dehover_node: function() {
            dehover_node.call(this);
            this.get_container().trigger('dehover_node');
        },

        /**
         * @override
         */
        select_node: function() {
            select_node.apply(this, arguments);
            this.data.ui.last_selected.trigger('select_tree_node');
        }
    });

    $.widget('mage.treeSuggest', $.mage.multisuggest, {
        /**
         * @override
         */
        _bind: function() {
            this._super();
            this._on({
                keydown: function(event) {
                    var keyCode = $.ui.keyCode;
                    switch (event.keyCode) {
                        case keyCode.LEFT:
                        case keyCode.RIGHT:
                            if (this.isDropdownShown()) {
                                event.preventDefault();
                                this._proxyEvents(event);
                            }
                    }
                }
            });
            this._on({
                focus: function() {
                    this.search();
                }
            });
        },

        /**
         * @override
         */
        search: function() {
            if (!this.options.showRecent && !this._value()) {
                this._showAll();
            } else {
                this._super();
            }
        },

        /**
         * @override
         * @private
         */
        _prepareDropdownContext: function() {
            var context = this._superApply(arguments),
                optionData = context.optionData,
                templateName = this.templateName;
                context.optionData = function(item) {
                    item = $.extend({}, item);
                    delete item.children;
                    return optionData(item);
                };
            return $.extend(context, {
                renderTreeLevel: function(children) {
                    var _context = $.extend({}, this.data, {items: children, nested: true});
                    return $('<div>').append($.tmpl(templateName, _context)).html();
                }
            });
        },

        /**
         * @override
         */
        _renderDropdown: function() {
            var control = this.dropdown.find(this._control.selector);
            if (control.length && control.hasClass('jstree')) {
                control.jstree("destroy");
            }
            this._superApply(arguments);
        }
    });
})(jQuery);
