/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * Widget panel
     */
    $.widget('mage.sortable', $.ui.sortable, {
        options: {
            moveUpEvent:   'moveUp',
            moveDownEvent: 'moveDown'
        },

        _create: function() {
            this._super();
            this._bind();
        },

        _bind: function() {
            var $body = $('body');

            this.element.find('input.up').on('click', $.proxy(function(event){
                $body.trigger(this.options.moveUpEvent, {item:$(event.target).parent('li')});
            }, this));
            this.element.find('input.down').on('click', $.proxy(function(event){
                $body.trigger(this.options.moveDownEvent, {item:$(event.target).parent('li')});
            }, this));

            $body.on(this.options.moveUpEvent, $.proxy(this._onMoveUp, this));
            $body.on(this.options.moveDownEvent, $.proxy(this._onMoveDown, this));
        },

        _onMoveUp: function(event, data) {
            data.item.insertBefore(data.item.prev());
            console.log(this.serialize());
        },

        _onMoveDown: function(event, data) {
            data.item.insertAfter(data.item.next());
        }
    });

})(jQuery);
