/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($){

    /**
     * Widget tree
     */
    $.widget('vde.vde_tree', {
        options: {
            ui: {
                select_limit: 1,
                selected_parent_close: false
            },
            themes: {
                dots: false,
                icons: false
            },
            callback: {
                onselect: function(e, data){
                    var link = $(data.rslt.obj).find('a:first');
                    $(this)
                        .trigger('change_title.vde_menu', [$.trim(link.text())])
                        .trigger('link_selected.vde_tree', [function () {window.location = link.attr('href');}]);
                }
            }
        },
        _create: function () {
            var self = this;
            this.element.on('loaded.jstree' , function(e, data){
                if (self.element.data('selected')) {
                    self.element.jstree('select_node' , self.element.find(self.element.data('selected')));
                }
            });
            this.element.jstree(this.options);
            this.element.on('select_node.jstree', this.options.callback.onselect);
        }
    });

    /**
     * Widget menu
     */
    $.widget('vde.vde_menu', {
        options: {
            type: 'popup',
            titleSelector: ':first-child',
            titleTextSelector : '.vde_toolbar_cell_value',
            activeClass : 'active'
        },
        _create: function () {
            this._bind();
            if (this.options.treeSelector) {
                if (this.element.find(this.options.treeSelector).size()) {
                    this.element.find(this.options.treeSelector).vde_tree()
                }
            }
            var self = this;
            if (this.options.slimScroll) {
                if (this.options.treeSelector && this.element.find(this.options.treeSelector).size()) {
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
        },
        _bind: function () {
            var self = this;
            this.element
                .on('hide.' + self.widgetName, function(e){self.hide(e)})
                .on('change_title.' + self.widgetName, function(e, title){
                    self.element.find(self.options.titleTextSelector).text(title);
                })
                .on('link_selected.vde_tree', function(e, done){
                    if (self.element.hasClass('active')) {
                        self.element.removeClass('active');
                        done();
                    }
                })
                .find(this.options.titleSelector).first()
                .on('click.' + self.widgetName, function(e){
                    self.element.hasClass(self.options.activeClass) ?
                        self.hide(e):
                        self.show(e);
                })
            $('body').on('click', function(e){
                var widgetInstancesSelector = ':' + self.namespace + '-' + self.widgetName;
                $(widgetInstancesSelector).not($(e.target).parents(widgetInstancesSelector).first()).vde_menu('hide');
            })
        },
        show: function(e){
            this.element.addClass(this.options.activeClass).trigger('activate_toolbar_cell.' + this.widgetName);
        },
        hide: function(e){
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
        }
    });
})(jQuery);