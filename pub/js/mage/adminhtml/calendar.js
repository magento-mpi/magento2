/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Calendar
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * Widget calendar
     */
    $.widget('mage.calendar', {
        _create: function () {
            this.options = $.extend({}, this.options, $.calendarConfig ? $.calendarConfig : {});
            this._initPicker(this.element, this.options);
        },
        _initPicker: function(element) {
            element.datetimepicker(this.options);
        }
    });

    /**
     * Widget date_range
     */
    $.widget('mage.date_range', $.mage.calendar, {
        _initPicker: function(){
            if(this.options.from && this.options.to) {
                var from = this.element.find('#' + this.options.from.id)
                var to = this.element.find('#' + this.options.to.id );
                this.options.onSelect = function( selectedDate ) {
                    to.datepicker( "option", "minDate", selectedDate );
                }
                $.mage.calendar.prototype._initPicker.apply(this, [from]);
                this.options.onSelect = function( selectedDate ) {
                    from.datepicker( "option", "maxDate", selectedDate );
                }
                $.mage.calendar.prototype._initPicker.apply(this, [to]);
            }
        }
    })

})( jQuery );
