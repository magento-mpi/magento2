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
    var hover_node = $.jstree._instance.prototype.hover_node;
    var dehover_node = $.jstree._instance.prototype.dehover_node;
    var select_node = $.jstree._instance.prototype.select_node;
    var init = $.jstree._instance.prototype.init;
    $.extend(true, $.jstree._instance.prototype, {
        init: function() {
            this.get_container().on('keydown', $.proxy(function(e) {
                if(e.keyCode === $.ui.keyCode.ENTER) {
                    var o = this.data.ui.hovered || this.data.ui.last_selected || -1;
                    this.select_node(o);
                }
            }, this));
            init.call(this);
        },
        hover_node: function(obj) {
            hover_node.apply(this, arguments);
            var node = $(this._get_node(obj).is('a') ? this._get_node(obj) : this._get_node(obj).children("a"));
            this.get_container().trigger('hover_node', [{item: (node.is('a') ? node : node.find('a:first'))}]);
        },
        dehover_node: function() {
            dehover_node.call(this);
            this.get_container().trigger('dehover_node');
        },
        select_node: function() {
            select_node.apply(this, arguments);
            this.data.ui.last_selected.trigger('select_tree_node');
        }
    });
})(jQuery);