/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * Widget menu
     */
    $.widget('vde.vde_menu', {
        options: {
            type: 'popup',
            titleSelector: '.vde_toolbar_cell_title',
            titleTextSelector: '.vde_toolbar_cell_value',
            activeClass: 'active'
        },
        _create: function () {
            this._bind();
            if (this.options.treeSelector) {
                var tree = this.element.find(this.options.treeSelector);
                if (tree.size()) {
                    tree.vde_tree();
                    if (this.options.slimScroll) {
                        var self = this;
                        this.element
                            .one('activate_toolbar_cell.' + self.widgetName, function () {
                                self.element.find(self.options.treeSelector).slimScroll({
                                    color: '#cccccc',
                                    alwaysVisible: true,
                                    opacity: 1,
                                    height: 'auto',
                                    size: 9
                                });
                        })
                    }
                }
            }
        },
        _bind: function () {
            var self = this,
                titleText = self.element.find(self.options.titleTextSelector);
            this.element
                .on('change_title.' + self.widgetName, function(e, title) {
                    titleText.text(title);
                })
                .on('link_selected.vde_tree', function(e, link) {
                    titleText.text(link.text());
                    self.hide(e);
                })
                .find(this.options.titleSelector)
                .on('click.' + self.widgetName, function(e) {
                    self.element.hasClass(self.options.activeClass) ?
                        self.hide(e):
                        self.show(e);
                });
            $('body').on('click', function(e) {
                var widgetInstancesSelector = ':' + self.namespace + '-' + self.widgetName;
                $(widgetInstancesSelector).not($(e.target).parents(widgetInstancesSelector)).vde_menu('hide');
            })
        },
        show: function() {
            this.element.addClass(this.options.activeClass).trigger('activate_toolbar_cell.' + this.widgetName);
        },
        hide: function() {
            this.element.removeClass(this.options.activeClass);
        }
    });

    /**
     * Widget checkbox
     */
    $.widget('vde.vde_checkbox', {
        options: {
            checkedClass: 'checked'
        },
        _create: function () {
            this._bind();
        },
        _bind: function () {
            var self = this;
            this.element.on('click', function () {
                self._click();
            })
        },
        _click: function () {
            if (this.element.hasClass(this.options.checkedClass)) {
                this.element.removeClass(this.options.checkedClass);
                this.element.trigger('unchecked.' + this.widgetName);
            } else {
                this.element.addClass(this.options.checkedClass);
                this.element.trigger('checked.' + this.widgetName);
            }
        },
        setChecked: function() {
            if (!this.element.hasClass(this.options.checkedClass)) {
                this.element.addClass(this.options.checkedClass);
            }
        }
    });
})(jQuery);
