/**
 * Globalize Culture am
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

Globalize.addCultureInfo( "am", "default", {
	name: "am",
	englishName: "Amharic",
	nativeName: "አማርኛ",
	language: "am",
	numberFormat: {
		decimals: 1,
		groupSizes: [3,0],
		"NaN": "NAN",
		percent: {
			pattern: ["-n%","n%"],
			decimals: 1,
			groupSizes: [3,0]
		},
		currency: {
			pattern: ["-$n","$n"],
			groupSizes: [3,0],
			symbol: "ETB"
		}
	},
	calendars: {
		standard: {
			days: {
				names: ["እሑድ","ሰኞ","ማክሰኞ","ረቡዕ","ሐሙስ","ዓርብ","ቅዳሜ"],
				namesAbbr: ["እሑድ","ሰኞ","ማክሰ","ረቡዕ","ሐሙስ","ዓርብ","ቅዳሜ"],
				namesShort: ["እ","ሰ","ማ","ረ","ሐ","ዓ","ቅ"]
			},
			months: {
				names: ["ጃንዩወሪ","ፌብሩወሪ","ማርች","ኤፕረል","ሜይ","ጁን","ጁላይ","ኦገስት","ሴፕቴምበር","ኦክተውበር","ኖቬምበር","ዲሴምበር",""],
				namesAbbr: ["ጃንዩ","ፌብሩ","ማርች","ኤፕረ","ሜይ","ጁን","ጁላይ","ኦገስ","ሴፕቴ","ኦክተ","ኖቬም","ዲሴም",""]
			},
			AM: ["ጡዋት","ጡዋት","ጡዋት"],
			PM: ["ከሰዓት","ከሰዓት","ከሰዓት"],
			eras: [{"name":"ዓመተ  ምሕረት","start":null,"offset":0}],
			patterns: {
				d: "d/M/yyyy",
				D: "dddd '፣' MMMM d 'ቀን' yyyy",
				f: "dddd '፣' MMMM d 'ቀን' yyyy h:mm tt",
				F: "dddd '፣' MMMM d 'ቀን' yyyy h:mm:ss tt",
				M: "MMMM d ቀን",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
