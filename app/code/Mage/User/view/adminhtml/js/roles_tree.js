jQuery(function($) {
    'use strict';
    $.widget('mage.rolesTree', {
        options: {
            treeInitData: {},
            treeInitSelectedData: {}
        },
        _create: function() {
            this.element.jstree({
                plugins: ["themes", "json_data", "ui", "crrm", "types", "checkbox", "hotkeys"],
                checkbox: {
                    'real_checkboxes': true,
                    'real_checkboxes_names': function(n) {return ['resource[]', $(n).data('id')]}
                },
                json_data: {data: this.options.treeInitData},
                ui: {select_limit: 0},
                hotkeys: {
                    space: this._changeState,
                    'return': this._changeState
                }
            });
            this._bind();
        },
        _destroy: function() {
            this.element.jstree('destroy');
        },
        _bind: function() {
            this.element.on('loaded.jstree', $.proxy(this._checkNodes, this));
            this.element.delegate("li", "click.jstree", $.proxy(this._checkNode, this));
        },
        _checkNode: function(e) {
            e.stopPropagation();
            this.element.jstree('change_state', e.currentTarget, this.element.jstree('is_checked', e.currentTarget));
        },
        _checkNodes: function() {
            var selected = this.options.treeInitSelectedData;
            selected = selected.filter($.proxy(function(item) {
                return this.element.jstree('is_leaf', '[data-id="' + item + '"]');
            }, this));
            this.element.jstree('check_node', '[data-id="' + selected.join('"],[data-id="') + '"]');
        },
        _changeState: function() {
            if (this.data.ui.hovered) {
                var element = this.data.ui.hovered;
                this.change_state(element, this.is_checked(element));
            }
            return false;
        }
    });
});