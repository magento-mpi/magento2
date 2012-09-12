/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {

    /**
     * Widget calendar
     */
    $.widget('mage.calendar', {
        /**
         * Merge global options with options passed to widget invoke
         * @protected
         */
        _create: function() {
            this._enableAMPM();
            this.options = $.extend(
                {},
                $.calendarConfig ? $.calendarConfig : {},
                this.options.showsTime ? {
                    showTime: true,
                    showHour: true,
                    showMinute: true
                } : {},
                this.options
            );
            this._initPicker(this.element);
        },

        /**
         * Fix for Timepicker - Set ampm option for Timepicker if timeformat contains string 'tt'
         * @protected
         */
        _enableAMPM: function() {
            if (this.options.timeFormat && this.options.timeFormat.indexOf('tt') >= 0) {
                this.options.ampm = true;
            }
        },

        /**
         * If server timezone is defined then take to account server timezone shift
         * @param {Date}
         * @return {Date}
         */
        getTimezoneDate: function(date) {
            date = date || new Date();
            if (this.options.serverTimezoneSeconds) {
                date.setTime((this.options.serverTimezoneSeconds + date.getTimezoneOffset() * 60) * 1000);
            }
            return date;
        },

        /**
         * Set current date if the date is not set
         * @protected
         * @param {Element}
         */
        _setCurrentDate: function(element) {
            if (!element.val()) {
                element
                    .datetimepicker('setDate', this.getTimezoneDate())
                    .val('')
            }
        },

        /**
         * Init Datetimepicker
         * @protected
         * @param {Element}
         */
        _initPicker: function(element) {
            element
                .datetimepicker(this.options)
                .next('.ui-datepicker-trigger')
                .addClass('v-middle');
            this._setCurrentDate(element);
        },

        /**
         * destroy instance of datetimepicker
         */
        destroy: function(){
            this.element.datetimepicker('destroy');
            $.Widget.prototype.destroy.call(this);
        }
    });

    /**
     * Extension for Calendar - date and time format convert functionality
     * @var {Object}
     */
    var calendarBasePrototype = $.mage.calendar.prototype;
    $.widget('mage.calendar', $.extend({}, calendarBasePrototype,
        /** @lends {$.mage.calendar.prototype} */ {
            /**
             * key - backend format, value - jquery format
             * @type {Object}
             * @private
             */
            dateTimeFormat: {
                date: {
                    'EEEE': 'DD',
                    'EEE': 'D',
                    'D': 'o',
                    'MMMM': 'MM',
                    'MMM': 'M',
                    'MM': 'mm',
                    'M': 'mm',
                    'yyyy': 'yy',
                    'y': 'yy',
                    'yy': 'yy'
                },
                time: {
                    'a': 'tt',
                    'HH': 'hh',
                    'H': 'h'
                }
            },

            /**
             * Add Date and Time converting to _create method
             * @protected
             */
            _create: function() {
                if (this.options.dateFormat) {
                    this.options.dateFormat = this._convertFormat(this.options.dateFormat, 'date');
                }
                if (this.options.timeFormat) {
                    this.options.timeFormat = this._convertFormat(this.options.timeFormat, 'time');
                }
                calendarBasePrototype._create.apply(this, arguments);
            },

            /**
             * Converting date or time format
             * @protected
             * @param {string}
             * @param {string}
             * @return {string}
             */
            _convertFormat: function(format, type) {
                var symbols = format.match(/([a-z]+)/ig),
                    separators = format.match(/([^a-z]+)/ig),
                    self = this;
                convertedFormat = '';
                if (symbols) {
                    $.each(symbols, function(key, val) {
                        convertedFormat +=
                            (self.dateTimeFormat[type][val] || val) +
                            (separators[key] || '');
                    });
                }
                return convertedFormat;
            }
    }));

    /**
     * Widget dateRange
     * @extends $.mage.calendar
     */
    $.widget('mage.dateRange', $.mage.calendar, {
        /**
         * creates two instances of datetimepicker for date range selection
         * @protected
         */
        _initPicker: function() {
            if (this.options.from && this.options.to) {
                var from = this.element.find('#' + this.options.from.id),
                    to = this.element.find('#' + this.options.to.id);
                this.options.onSelect = function(selectedDate) {
                    to.datetimepicker('option', 'minDate', selectedDate);
                }
                $.mage.calendar.prototype._initPicker.call(this, from);
                this.options.onSelect = function(selectedDate) {
                    from.datetimepicker('option', 'maxDate', selectedDate);
                }
                $.mage.calendar.prototype._initPicker.call(this, to);
            }
        },

        /**
         * destroy two instances of datetimepicker
         */
        destroy: function(){
            if(this.options.from) {
                this.element.find('#' + this.options.from.id).datetimepicker('destroy');
            }
            if(this.options.to) {
                this.element.find('#' + this.options.to.id).datetimepicker('destroy');
            }
            $.Widget.prototype.destroy.call(this);
        }
    })
})(jQuery);
