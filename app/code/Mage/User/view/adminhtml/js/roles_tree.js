/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
jQuery(function($) {
    'use strict';
    $.widget('mage.rolesTree', {
        options: {
            treeInitData: {},
            treeInitSelectedData: {}
        },
        _create: function() {
            this.element.jstree({
                plugins: ["themes", "json_data", "ui", "crrm", "types", "vcheckbox", "hotkeys"],
                vcheckbox: {
                    'two_state': true,
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
            this.element.on('click.jstree', 'li', $.proxy(this._checkNode, this));
        },
        _checkNode: function(event) {
            event.stopPropagation();
            this.element.jstree(
                'change_state',
                event.currentTarget,
                this.element.jstree('is_checked', event.currentTarget)
            );
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