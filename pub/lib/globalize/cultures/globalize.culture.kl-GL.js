/**
 * Globalize Culture kl-GL
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

Globalize.addCultureInfo( "kl-GL", "default", {
	name: "kl-GL",
	englishName: "Greenlandic (Greenland)",
	nativeName: "kalaallisut (Kalaallit Nunaat)",
	language: "kl",
	numberFormat: {
		",": ".",
		".": ",",
		groupSizes: [3,0],
		negativeInfinity: "-INF",
		positiveInfinity: "INF",
		percent: {
			groupSizes: [3,0],
			",": ".",
			".": ","
		},
		currency: {
			pattern: ["$ -n","$ n"],
			groupSizes: [3,0],
			",": ".",
			".": ",",
			symbol: "kr."
		}
	},
	calendars: {
		standard: {
			"/": "-",
			firstDay: 1,
			days: {
				names: ["sapaat","ataasinngorneq","marlunngorneq","pingasunngorneq","sisamanngorneq","tallimanngorneq","arfininngorneq"],
				namesAbbr: ["sap","ata","mar","ping","sis","tal","arf"],
				namesShort: ["sa","at","ma","pi","si","ta","ar"]
			},
			months: {
				names: ["januari","februari","martsi","apriili","maaji","juni","juli","aggusti","septembari","oktobari","novembari","decembari",""],
				namesAbbr: ["jan","feb","mar","apr","mai","jun","jul","aug","sep","okt","nov","dec",""]
			},
			AM: null,
			PM: null,
			patterns: {
				d: "dd-MM-yyyy",
				D: "d. MMMM yyyy",
				t: "HH:mm",
				T: "HH:mm:ss",
				f: "d. MMMM yyyy HH:mm",
				F: "d. MMMM yyyy HH:mm:ss",
				M: "d. MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
