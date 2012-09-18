/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function($){
    $.widget("mage.editTrigger", {
        options: {
            img: 'http://magentojs.lo/pub/media/skin/adminhtml/default/default/enterprise/en_US/Mage_Core/fam_book_open.png',
            alt: '[TR]',
            template: '<img alt="${alt}" src="${img}">',
            zIndex: 2000,
            delay: 2000
        },
        /**
         * editTriger creation
         * @protected
         */
        _create: function() {
            $.template(this.widgetName, this.options.template);
            this.trigger = $.tmpl(this.widgetName, this.options)
                .css({position: 'absolute', cursor: 'pointer', display: 'none'})
                .on('click.' + this.widgetName, $.proxy(this._onClick, this))
                .appendTo('body');

            this._bind();
        },
        /**
         * Bind on mousemove event
         * @protected
         */
        _bind: function() {
            this.element.on('mousemove.' + this.widgetName, $.proxy(this._onMouseMove, this));
        },
        /**
         * Show editTriger
         */
        show: function() {
            this.trigger.show();
        },
        /**
         * Hide editTriger
         */
        hide: function() {
            this.trigger.hide();
            this._clearTimer();
            this.currentTarget = null;
        },
        /**
         * Set editTriger position
         * @protected
         */
        _setPosition: function(el) {
            var offset = el.offset(),
                triggerTopOffset = -3;
            this.trigger.css({
                top: offset.top + el.outerHeight() + triggerTopOffset,
                left: offset.left, 'z-index': this.options.zIndex
            });
        },
        /**
         * Clear timer
         * @protected
         */
        _clearTimer: function() {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
        },
        /**
         * Show/hide trigger on mouse move
         * @param {Object} event object
         * @protected
         */
        _onMouseMove: function(e) {
            var target = $(e.target);
            target = target.is(this.trigger) || target.is('[translate]') ?
                target :
                target.parents('[translate]').first();

            if (target.size()) {
                this._clearTimer();
                if (!target.is(this.trigger)) {
                    this._setPosition(target);
                    this.currentTarget = target;
                }
                if (this.trigger.is(':hidden')) {
                    this.show();
                }
            } else {
                if (this.trigger.is(':visible') && !this.timer) {
                    this.timer = setTimeout($.proxy(this.hide, this), this.options.delay);
                }
            }
        },
        /**
         * Trigger event "edit" on element for translate
         * @param {Object} event object
         * @protected
         */
        _onClick: function(e) {
            e.stopImmediatePropagation();
            $(this.currentTarget).trigger('edit.' + this.widgetName);
            this.hide();
        },
        /**
         * Destroy editTriger
         */
        destroy: function() {
            this.trigger.remove();
            this.element
                .off('mousemove.' + this.widgetName)
                .off('click.' + this.widgetName);
            return $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);