/**
 * Globalize Culture sr-Cyrl-RS
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

Globalize.addCultureInfo( "sr-Cyrl-RS", "default", {
	name: "sr-Cyrl-RS",
	englishName: "Serbian (Cyrillic, Serbia)",
	nativeName: "српски (Србија)",
	language: "sr-Cyrl",
	numberFormat: {
		",": ".",
		".": ",",
		negativeInfinity: "-бесконачност",
		positiveInfinity: "+бесконачност",
		percent: {
			pattern: ["-n%","n%"],
			",": ".",
			".": ","
		},
		currency: {
			pattern: ["-n $","n $"],
			",": ".",
			".": ",",
			symbol: "Дин."
		}
	},
	calendars: {
		standard: {
			"/": ".",
			firstDay: 1,
			days: {
				names: ["недеља","понедељак","уторак","среда","четвртак","петак","субота"],
				namesAbbr: ["нед","пон","уто","сре","чет","пет","суб"],
				namesShort: ["не","по","ут","ср","че","пе","су"]
			},
			months: {
				names: ["јануар","фебруар","март","април","мај","јун","јул","август","септембар","октобар","новембар","децембар",""],
				namesAbbr: ["јан","феб","мар","апр","мај","јун","јул","авг","сеп","окт","нов","дец",""]
			},
			AM: null,
			PM: null,
			eras: [{"name":"н.е.","start":null,"offset":0}],
			patterns: {
				d: "d.M.yyyy",
				D: "d. MMMM yyyy",
				t: "H:mm",
				T: "H:mm:ss",
				f: "d. MMMM yyyy H:mm",
				F: "d. MMMM yyyy H:mm:ss",
				M: "d. MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
