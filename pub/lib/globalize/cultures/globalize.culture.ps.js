/**
 * Globalize Culture ps
 *
 * http://github.com/jquery/globalize
 *
 * Copyright Software Freedom Conservancy, Inc.
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * This file was generated by the Globalize Culture Generator
 * Translation: bugs found in this file need to be fixed in the generator
 */

(function( window, undefined ) {

var Globalize;

if ( typeof require !== "undefined" &&
	typeof exports !== "undefined" &&
	typeof module !== "undefined" ) {
	// Assume CommonJS
	Globalize = require( "globalize" );
} else {
	// Global variable
	Globalize = window.Globalize;
}

Globalize.addCultureInfo( "ps", "default", {
	name: "ps",
	englishName: "Pashto",
	nativeName: "پښتو",
	language: "ps",
	isRTL: true,
	numberFormat: {
		pattern: ["n-"],
		",": "،",
		".": ",",
		"NaN": "غ ع",
		negativeInfinity: "-∞",
		positiveInfinity: "∞",
		percent: {
			pattern: ["%n-","%n"],
			",": "،",
			".": ","
		},
		currency: {
			pattern: ["$n-","$n"],
			",": "٬",
			".": "٫",
			symbol: "؋"
		}
	},
	calendars: {
		standard: {
			name: "Hijri",
			firstDay: 6,
			days: {
				names: ["الأحد","الإثنين","الثلاثاء","الأربعاء","الخميس","الجمعة","السبت"],
				namesAbbr: ["الأحد","الإثنين","الثلاثاء","الأربعاء","الخميس","الجمعة","السبت"],
				namesShort: ["ح","ن","ث","ر","خ","ج","س"]
			},
			months: {
				names: ["محرم","صفر","ربيع الأول","ربيع الثاني","جمادى الأولى","جمادى الثانية","رجب","شعبان","رمضان","شوال","ذو القعدة","ذو الحجة",""],
				namesAbbr: ["محرم","صفر","ربيع الأول","ربيع الثاني","جمادى الأولى","جمادى الثانية","رجب","شعبان","رمضان","شوال","ذو القعدة","ذو الحجة",""]
			},
			AM: ["غ.م","غ.م","غ.م"],
			PM: ["غ.و","غ.و","غ.و"],
			eras: [{"name":"بعد الهجرة","start":null,"offset":0}],
			twoDigitYearMax: 1451,
			patterns: {
				d: "dd/MM/yy",
				D: "dd/MM/yyyy",
				f: "dd/MM/yyyy h:mm tt",
				F: "dd/MM/yyyy h:mm:ss tt",
				M: "dd MMMM"
			},
			convert: {
                    // Adapted to Script from System.Globalization.HijriCalendar
                    ticks1970: 62135596800000,
                    // number of days leading up to each month
                    monthDays: [0, 30, 59, 89, 118, 148, 177, 207, 236, 266, 295, 325, 355],
                    minDate: -42521673600000,
                    maxDate: 253402300799999,
                    // The number of days to add or subtract from the calendar to accommodate the variances
                    // in the start and the end of Ramadan and to accommodate the date difference between
                    // countries/regions. May be dynamically adjusted based on user preference, but should
                    // remain in the range of -2 to 2, inclusive.
                    hijriAdjustment: 0,
                    toGregorian: function(hyear, hmonth, hday) {
                        var daysSinceJan0101 = this.daysToYear(hyear) + this.monthDays[hmonth] + hday - 1 - this.hijriAdjustment;
                        // 86400000 = ticks per day
                        var gdate = new Date(daysSinceJan0101 * 86400000 - this.ticks1970);
                        // adjust for timezone, because we are interested in the gregorian date for the same timezone
                        // but ticks in javascript is always from GMT, unlike the server were ticks counts from the base
                        // date in the current timezone.
                        gdate.setMinutes(gdate.getMinutes() + gdate.getTimezoneOffset());
                        return gdate;
                    },
                    fromGregorian: function(gdate) {
                        if ((gdate < this.minDate) || (gdate > this.maxDate)) return null;
                        var ticks = this.ticks1970 + (gdate-0) - gdate.getTimezoneOffset() * 60000,
                            daysSinceJan0101 = Math.floor(ticks / 86400000) + 1 + this.hijriAdjustment;
                        // very particular formula determined by someone smart, adapted from the server-side implementation.
                        // it approximates the hijri year.
                        var hday, hmonth, hyear = Math.floor(((daysSinceJan0101 - 227013) * 30) / 10631) + 1,
                            absDays = this.daysToYear(hyear),
                            daysInYear = this.isLeapYear(hyear) ? 355 : 354;
                        // hyear is just approximate, it may need adjustment up or down by 1.
                        if (daysSinceJan0101 < absDays) {
                            hyear--;
                            absDays -= daysInYear;
                        }
                        else if (daysSinceJan0101 === absDays) {
                            hyear--;
                            absDays = this.daysToYear(hyear);
                        }
                        else {
                            if (daysSinceJan0101 > (absDays + daysInYear)) {
                                absDays += daysInYear;
                                hyear++;
                            }
                        }
                        // determine month by looking at how many days into the hyear we are
                        // monthDays contains the number of days up to each month.
                        hmonth = 0;
                        var daysIntoYear = daysSinceJan0101 - absDays;
                        while (hmonth <= 11 && daysIntoYear > this.monthDays[hmonth]) {
                            hmonth++;
                        }
                        hmonth--;
                        hday = daysIntoYear - this.monthDays[hmonth];
                        return [hyear, hmonth, hday];
                    },
                    daysToYear: function(year) {
                        // calculates how many days since Jan 1, 0001
                        var yearsToYear30 = Math.floor((year - 1) / 30) * 30,
                            yearsInto30 = year - yearsToYear30 - 1,
                            days = Math.floor((yearsToYear30 * 10631) / 30) + 227013;
                        while (yearsInto30 > 0) {
                            days += (this.isLeapYear(yearsInto30) ? 355 : 354);
                            yearsInto30--;
                        }
                        return days;
                    },
                    isLeapYear: function(year) {
                        return ((((year * 11) + 14) % 30) < 11);
                    }
			}
		},
		Gregorian_Localized: {
			firstDay: 6,
			days: {
				names: ["یکشنبه","دوشنبه","سه\u200cشنبه","چارشنبه","پنجشنبه","جمعه","شنبه"],
				namesAbbr: ["یکشنبه","دوشنبه","سه\u200cشنبه","چارشنبه","پنجشنبه","جمعه","شنبه"],
				namesShort: ["ی","د","س","چ","پ","ج","ش"]
			},
			months: {
				names: ["سلواغه","كب","ورى","غويى","غبرګولى","چنګا ښزمرى","زمرى","وږى","تله","لړم","لنڈ ۍ","مرغومى",""],
				namesAbbr: ["سلواغه","كب","ورى","غويى","غبرګولى","چنګا ښ","زمرى","وږى","تله","لړم","لنڈ ۍ","مرغومى",""]
			},
			AM: ["غ.م","غ.م","غ.م"],
			PM: ["غ.و","غ.و","غ.و"],
			eras: [{"name":"ل.ه","start":null,"offset":0}],
			patterns: {
				d: "yyyy/M/d",
				D: "yyyy, dd, MMMM, dddd",
				f: "yyyy, dd, MMMM, dddd h:mm tt",
				F: "yyyy, dd, MMMM, dddd h:mm:ss tt",
				M: "d MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
