/**
 * Globalize Culture et-EE
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

Globalize.addCultureInfo( "et-EE", "default", {
	name: "et-EE",
	englishName: "Estonian (Estonia)",
	nativeName: "eesti (Eesti)",
	language: "et",
	numberFormat: {
		",": " ",
		".": ",",
		"NaN": "avaldamatu",
		negativeInfinity: "miinuslõpmatus",
		positiveInfinity: "plusslõpmatus",
		percent: {
			pattern: ["-n%","n%"],
			",": " ",
			".": ","
		},
		currency: {
			pattern: ["-n $","n $"],
			",": " ",
			symbol: "kr"
		}
	},
	calendars: {
		standard: {
			"/": ".",
			firstDay: 1,
			days: {
				names: ["pühapäev","esmaspäev","teisipäev","kolmapäev","neljapäev","reede","laupäev"],
				namesAbbr: ["P","E","T","K","N","R","L"],
				namesShort: ["P","E","T","K","N","R","L"]
			},
			months: {
				names: ["jaanuar","veebruar","märts","aprill","mai","juuni","juuli","august","september","oktoober","november","detsember",""],
				namesAbbr: ["jaan","veebr","märts","apr","mai","juuni","juuli","aug","sept","okt","nov","dets",""]
			},
			AM: ["EL","el","EL"],
			PM: ["PL","pl","PL"],
			patterns: {
				d: "d.MM.yyyy",
				D: "d. MMMM yyyy'. a.'",
				t: "H:mm",
				T: "H:mm:ss",
				f: "d. MMMM yyyy'. a.' H:mm",
				F: "d. MMMM yyyy'. a.' H:mm:ss",
				M: "d. MMMM",
				Y: "MMMM yyyy'. a.'"
			}
		}
	}
});

}( this ));
