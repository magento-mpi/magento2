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
        _create: function () {
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
            this._initPicker( this.element );
        },

        //Fix for iso am pm
        _enableAMPM: function() {
            if ( this.options.timeFormat && this.options.timeFormat.indexOf( 'tt' ) ) {
                this.options.ampm = true;
            }
        },

        //If server timezone is defined then take to account server timezone shift
        getTimezoneDate: function( date ) {
            date = date || new Date();
            if ( typeof( this.options.serverTimezoneSeconds ) != "undefined" ) {
                date.setTime( ( this.options.serverTimezoneSeconds + date.getTimezoneOffset() * 60 ) * 1000 );
            }
            return date;
        },

        //Set current date if the date is not setted
        _setCurrentDate: function( element ) {
            if ( !element.val() ) {
                element
                    .datetimepicker( 'setDate', this.getTimezoneDate() )
                    .val( '' )
            }
        },

        _initPicker: function( element ) {
            element
                .datetimepicker( this.options )
                .next( ".ui-datepicker-trigger" )
                .addClass( "v-middle" );
            this._setCurrentDate( element );
        }
    });

    /**
     * Extension for Calendar - date and time format convert functionality
     */
    var calendarBasePrototype = $.mage.calendar.prototype;
    $.widget('mage.calendar', $.extend({}, calendarBasePrototype, {
        dateTimeFormat: {
            // key - backend format, value - jquery format
            date: {
                "EEEE": "DD",
                "EEE": "D",
                "D": "o",
                "MMMM": "MM",
                "MMM": "M",
                "MM": "mm",
                "M": "mm",
                "yyyy": "yy",
                "y": "yy",
                "yy": "y"
            },
            time: {
                "a": "tt",
                "HH": "hh",
                "H": "h"
            }
        },
        _create: function() {
            if ( this.options.dateFormat ) {
                this.options.dateFormat = this._convertFormat( this.options.dateFormat, 'date' );
            }
            if ( this.options.timeFormat ) {
                this.options.timeFormat = this._convertFormat( this.options.timeFormat, 'time' );
            }
            calendarBasePrototype._create.apply( this, arguments );
        },
        _convertFormat: function( format, type ) {
            var symbols = format.match( /([a-z]+)/ig ),
                separators = format.match( /([^a-z]+)/ig ),
                self = this;
            convertedFormat = "";
            if ( symbols ) {
                $.each( symbols, function(key, val) {
                    convertedFormat +=
                        ( self.dateTimeFormat[type][val] || val ) +
                            ( separators[key] || '' );
                });
            }
            return convertedFormat;
        }
    }));

    /**
     * Widget date_range
     */
    $.widget('mage.date_range', $.mage.calendar, {
        _initPicker: function() {
            if ( this.options.from && this.options.to ) {
                var from = this.element.find( '#' + this.options.from.id ),
                    to = this.element.find( '#' + this.options.to.id );
                this.options.onSelect = function( selectedDate ) {
                    to.datetimepicker( "option", "minDate", selectedDate );
                }
                $.mage.calendar.prototype._initPicker.apply( this, [from] );
                this.options.onSelect = function( selectedDate ) {
                    from.datetimepicker( "option", "maxDate", selectedDate );
                }
                $.mage.calendar.prototype._initPicker.apply( this, [to] );
            }
        }
    })

})( jQuery );
