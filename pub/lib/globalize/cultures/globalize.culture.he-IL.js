/**
 * Globalize Culture he-IL
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

Globalize.addCultureInfo( "he-IL", "default", {
	name: "he-IL",
	englishName: "Hebrew (Israel)",
	nativeName: "עברית (ישראל)",
	language: "he",
	isRTL: true,
	numberFormat: {
		"NaN": "לא מספר",
		negativeInfinity: "אינסוף שלילי",
		positiveInfinity: "אינסוף חיובי",
		percent: {
			pattern: ["-n%","n%"]
		},
		currency: {
			pattern: ["$-n","$ n"],
			symbol: "₪"
		}
	},
	calendars: {
		standard: {
			days: {
				names: ["יום ראשון","יום שני","יום שלישי","יום רביעי","יום חמישי","יום שישי","שבת"],
				namesAbbr: ["יום א","יום ב","יום ג","יום ד","יום ה","יום ו","שבת"],
				namesShort: ["א","ב","ג","ד","ה","ו","ש"]
			},
			months: {
				names: ["ינואר","פברואר","מרץ","אפריל","מאי","יוני","יולי","אוגוסט","ספטמבר","אוקטובר","נובמבר","דצמבר",""],
				namesAbbr: ["ינו","פבר","מרץ","אפר","מאי","יונ","יול","אוג","ספט","אוק","נוב","דצמ",""]
			},
			eras: [{"name":"לספירה","start":null,"offset":0}],
			patterns: {
				d: "dd/MM/yyyy",
				D: "dddd dd MMMM yyyy",
				t: "HH:mm",
				T: "HH:mm:ss",
				f: "dddd dd MMMM yyyy HH:mm",
				F: "dddd dd MMMM yyyy HH:mm:ss",
				M: "dd MMMM",
				Y: "MMMM yyyy"
			}
		},
		Hebrew: {
			name: "Hebrew",
			"/": " ",
			days: {
				names: ["יום ראשון","יום שני","יום שלישי","יום רביעי","יום חמישי","יום שישי","שבת"],
				namesAbbr: ["א","ב","ג","ד","ה","ו","ש"],
				namesShort: ["א","ב","ג","ד","ה","ו","ש"]
			},
			months: {
				names: ["תשרי","חשון","כסלו","טבת","שבט","אדר","אדר ב","ניסן","אייר","סיון","תמוז","אב","אלול"],
				namesAbbr: ["תשרי","חשון","כסלו","טבת","שבט","אדר","אדר ב","ניסן","אייר","סיון","תמוז","אב","אלול"]
			},
			eras: [{"name":"C.E.","start":null,"offset":0}],
			twoDigitYearMax: 5790,
			patterns: {
				d: "dd MMMM yyyy",
				D: "dddd dd MMMM yyyy",
				t: "HH:mm",
				T: "HH:mm:ss",
				f: "dddd dd MMMM yyyy HH:mm",
				F: "dddd dd MMMM yyyy HH:mm:ss",
				M: "dd MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
