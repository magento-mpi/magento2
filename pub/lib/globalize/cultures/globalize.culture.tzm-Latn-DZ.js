/**
 * Globalize Culture tzm-Latn-DZ
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

Globalize.addCultureInfo( "tzm-Latn-DZ", "default", {
	name: "tzm-Latn-DZ",
	englishName: "Tamazight (Latin, Algeria)",
	nativeName: "Tamazight (Djazaïr)",
	language: "tzm-Latn",
	numberFormat: {
		pattern: ["n-"],
		",": ".",
		".": ",",
		"NaN": "Non Numérique",
		negativeInfinity: "-Infini",
		positiveInfinity: "+Infini",
		percent: {
			",": ".",
			".": ","
		},
		currency: {
			pattern: ["-n $","n $"],
			symbol: "DZD"
		}
	},
	calendars: {
		standard: {
			"/": "-",
			firstDay: 6,
			days: {
				names: ["Acer","Arime","Aram","Ahad","Amhadh","Sem","Sedh"],
				namesAbbr: ["Ace","Ari","Ara","Aha","Amh","Sem","Sed"],
				namesShort: ["Ac","Ar","Ar","Ah","Am","Se","Se"]
			},
			months: {
				names: ["Yenayer","Furar","Maghres","Yebrir","Mayu","Yunyu","Yulyu","Ghuct","Cutenber","Ktuber","Wambir","Dujanbir",""],
				namesAbbr: ["Yen","Fur","Mag","Yeb","May","Yun","Yul","Ghu","Cut","Ktu","Wam","Duj",""]
			},
			AM: null,
			PM: null,
			patterns: {
				d: "dd-MM-yyyy",
				D: "dd MMMM, yyyy",
				t: "H:mm",
				T: "H:mm:ss",
				f: "dd MMMM, yyyy H:mm",
				F: "dd MMMM, yyyy H:mm:ss",
				M: "dd MMMM"
			}
		}
	}
});

}( this ));
