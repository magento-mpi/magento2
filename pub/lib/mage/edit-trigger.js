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
            img: '',
            alt: '[TR]',
            template: '<img alt="${alt}" src="${img}">',
            zIndex: 2000,
            editSelector: '[data-translate]',
            delay: 2000,
            offsetTop: -3
        },
        /**
         * editTriger creation
         * @protected
         */
        _create: function() {
            $.template(this.widgetName, this.options.template);
            this.trigger = $.tmpl(this.widgetName, this.options)
                .css({
                    position: 'absolute',
                    cursor: 'pointer',
                    display: 'none',
                    'z-index': this.options.zIndex
                })
                .appendTo('body');

            this._bind();
        },
        /**
         * Bind on mousemove event
         * @protected
         */
        _bind: function() {
            this.trigger.on('click.' + this.widgetName, $.proxy(this._onClick, this));
            this.element.on('mousemove.' + this.widgetName, $.proxy(this._onMouseMove, this));
        },
        /**
         * Show editTriger
         */
        show: function() {
            if (this.trigger.is(':hidden')) {
                this.trigger.show();
            }
        },
        /**
         * Hide editTriger
         */
        hide: function() {
            this.currentTarget = null;
            if (this.trigger.is(':visible')) {
                this.trigger.hide();
            }
        },
        /**
         * Set editTriger position
         * @protected
         */
        _setPosition: function(el) {
            var offset = el.offset();
            this.trigger.css({
                top: offset.top + el.outerHeight() + this.options.offsetTop,
                left: offset.left
            });
        },
        /**
         * Show/hide trigger on mouse move
         * @param {Object} event object
         * @protected
         */
        _onMouseMove: function(e) {
            var target = $(e.target);
            target = target.is(this.trigger) || target.is(this.options.editSelector) ?
                target :
                target.parents(this.options.editSelector).first();

            if (target.size()) {
                if (!target.is(this.trigger)) {
                    this._setPosition(target);
                    this.currentTarget = target;
                }
                this.show();
            } else {
                this.hide();
            }
        },
        /**
         * Trigger event "edit" on element for translate
         * @param {Object} event object
         * @protected
         */
        _onClick: function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $(this.currentTarget).trigger('edit.' + this.widgetName);
            this.trigger.hide();
        },
        /**
         * Destroy editTriger
         */
        destroy: function() {
            this.trigger.remove();
            this.element.off('.' + this.widgetName);
            return $.Widget.prototype.destroy.call(this);
        }
    });

    /**
     * Extention for widget editTrigger - hide trigger with delay
     */
    var editTriggerPrototype = $.mage.editTrigger.prototype;
    $.widget("mage.editTrigger", $.extend({}, editTriggerPrototype, {
        /**
         * Added clear timeout on trigger show
         */
        show: function() {
            editTriggerPrototype.show.apply(this, arguments);
            if(this.options.delay){
                this._clearTimer();
            }
        },
        /**
         * Added setTimeout on trigger hide
         */
        hide: function() {
            if(this.options.delay){
                if(!this.timer){
                    this.timer = setTimeout($.proxy(function() {
                        editTriggerPrototype.hide.apply(this, arguments);
                        this._clearTimer();
                    }, this), this.options.delay);
                }
            } else {
                editTriggerPrototype.hide.apply(this, arguments);
            }
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
        }
    }));
})(jQuery);