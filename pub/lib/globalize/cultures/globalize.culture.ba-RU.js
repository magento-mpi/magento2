/**
 * Globalize Culture ba-RU
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

Globalize.addCultureInfo( "ba-RU", "default", {
	name: "ba-RU",
	englishName: "Bashkir (Russia)",
	nativeName: "Башҡорт (Россия)",
	language: "ba",
	numberFormat: {
		",": " ",
		".": ",",
		groupSizes: [3,0],
		negativeInfinity: "-бесконечность",
		positiveInfinity: "бесконечность",
		percent: {
			pattern: ["-n%","n%"],
			groupSizes: [3,0],
			",": " ",
			".": ","
		},
		currency: {
			pattern: ["-n $","n $"],
			groupSizes: [3,0],
			",": " ",
			".": ",",
			symbol: "һ."
		}
	},
	calendars: {
		standard: {
			"/": ".",
			firstDay: 1,
			days: {
				names: ["Йәкшәмбе","Дүшәмбе","Шишәмбе","Шаршамбы","Кесаҙна","Йома","Шәмбе"],
				namesAbbr: ["Йш","Дш","Шш","Шр","Кс","Йм","Шб"],
				namesShort: ["Йш","Дш","Шш","Шр","Кс","Йм","Шб"]
			},
			months: {
				names: ["ғинуар","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь",""],
				namesAbbr: ["ғин","фев","мар","апр","май","июн","июл","авг","сен","окт","ноя","дек",""]
			},
			AM: null,
			PM: null,
			patterns: {
				d: "dd.MM.yy",
				D: "d MMMM yyyy 'й'",
				t: "H:mm",
				T: "H:mm:ss",
				f: "d MMMM yyyy 'й' H:mm",
				F: "d MMMM yyyy 'й' H:mm:ss",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
