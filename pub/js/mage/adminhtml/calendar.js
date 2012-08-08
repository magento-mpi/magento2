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
            this._convertTimeFormat();
            this.options = $.extend(
                {},
                $.calendarConfig ? $.calendarConfig : {},
                this.options.showsTime ? {showTime: true, showHour: true, showMinute: true} : {},
                this.options
            );
            this._initPicker(this.element);
        },
        //Fix for iso am pm
        _convertTimeFormat: function(){
            if(this.options.timeFormat && $.type(this.options.timeFormat) === 'string') {
                var ampm = this.options.timeFormat.indexOf('a');
                if(ampm >= 0) {
                    this.options.timeFormat = this.options.timeFormat.replace('a', 'TT');
                    this.options.ampm = true;
                }
            }
        },
        /*getTimezoneDate: function(date){
            date = date || new Date();
            if(typeof(this.options.serverTimezoneSeconds) != "undefined"){
                date.setTime((this.options.serverTimezoneSeconds + date.getTimezoneOffset()*60)*1000);
            }
            return date;
        },*/
        _initPicker: function(element) {
            element.datetimepicker(this.options).next(".ui-datepicker-trigger").addClass("v-middle");
        }
    });

    /**
     * Widget date_range
     */
    $.widget('mage.date_range', $.mage.calendar, {
        _initPicker: function(){
            if(this.options.from && this.options.to) {
                var from = this.element.find('#' + this.options.from.id),
                    to = this.element.find('#' + this.options.to.id ),
                    self = this;
                this.options.onSelect = function( selectedDate ) {
                    self._onSelect(to, selectedDate, "minDate");
                }
                $.mage.calendar.prototype._initPicker.apply(this, [from]);
                this.options.onSelect = function( selectedDate ) {
                    self._onSelect(from, selectedDate, "maxDate");
                }
                $.mage.calendar.prototype._initPicker.apply(this, [to]);
            }
        },
        _onSelect: function(element, selectedDate, option){
            element.datepicker( "option", option, selectedDate );
        }
    })

})( jQuery );
