/**
 * Globalize Culture uz-Latn-UZ
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

Globalize.addCultureInfo( "uz-Latn-UZ", "default", {
	name: "uz-Latn-UZ",
	englishName: "Uzbek (Latin, Uzbekistan)",
	nativeName: "U'zbek (U'zbekiston Respublikasi)",
	language: "uz-Latn",
	numberFormat: {
		",": " ",
		".": ",",
		percent: {
			pattern: ["-n%","n%"],
			",": " ",
			".": ","
		},
		currency: {
			pattern: ["-n $","n $"],
			decimals: 0,
			",": " ",
			".": ",",
			symbol: "so'm"
		}
	},
	calendars: {
		standard: {
			firstDay: 1,
			days: {
				names: ["yakshanba","dushanba","seshanba","chorshanba","payshanba","juma","shanba"],
				namesAbbr: ["yak.","dsh.","sesh.","chr.","psh.","jm.","sh."],
				namesShort: ["ya","d","s","ch","p","j","sh"]
			},
			months: {
				names: ["yanvar","fevral","mart","aprel","may","iyun","iyul","avgust","sentyabr","oktyabr","noyabr","dekabr",""],
				namesAbbr: ["yanvar","fevral","mart","aprel","may","iyun","iyul","avgust","sentyabr","oktyabr","noyabr","dekabr",""]
			},
			AM: null,
			PM: null,
			patterns: {
				d: "dd/MM yyyy",
				D: "yyyy 'yil' d-MMMM",
				t: "HH:mm",
				T: "HH:mm:ss",
				f: "yyyy 'yil' d-MMMM HH:mm",
				F: "yyyy 'yil' d-MMMM HH:mm:ss",
				M: "d-MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
