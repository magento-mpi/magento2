jQuery(function($) {
    'use strict';
    $.widget('mage.rolesTree', {
        options: {
            selectors: {
                saveInput: null,
                roleForm: null
            },
            treeInitData: {},
            treeInitSelectedData: {}
        },
        _create: function() {
            this.element.jstree({
                plugins: ["themes", "json_data", "ui", "crrm", "types", "checkbox", "hotkeys"],
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
            $(this.options.selectors.roleForm).off('save.mage');
            this.element.jstree('destroy');
        },
        _bind: function() {
            this.element.on('loaded.jstree', $.proxy(this._checkNodes, this));
            this.element.delegate("li", "click.jstree", $.proxy(this._checkNode, this));
            $(this.options.selectors.roleForm).on('save.mage', $.proxy(this._serializeData, this));
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
        _getChecked: function() {
            return this.element.jstree('get_container').find(".jstree-checked, .jstree-undetermined");
        },
        _serializeData: function() {
            var checked = this._getChecked.call(this),
                r = [];
            $.each(checked.map(function(){return $(this).data('id')}), function(k, v) {
                r.push(v);
            });
            $(this.options.selectors.saveInput).val(r.join(','));
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