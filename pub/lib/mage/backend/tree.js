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
    $.extend(true, $.jstree._instance.prototype, {
        hover_node: function(obj){
            hover_node.apply(this, arguments);
            this.get_container().trigger('hover_node', [{item: this._get_node(obj).children("a")}]);
        },
        dehover_node: function(){
            dehover_node.call(this);
            this.get_container().trigger('dehover_node');
        }
    });
})(jQuery);