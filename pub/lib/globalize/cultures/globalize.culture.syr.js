/**
 * Globalize Culture syr
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

Globalize.addCultureInfo( "syr", "default", {
	name: "syr",
	englishName: "Syriac",
	nativeName: "ܣܘܪܝܝܐ",
	language: "syr",
	isRTL: true,
	numberFormat: {
		currency: {
			pattern: ["$n-","$ n"],
			symbol: "ل.س.\u200f"
		}
	},
	calendars: {
		standard: {
			firstDay: 6,
			days: {
				names: ["ܚܕ ܒܫܒܐ","ܬܪܝܢ ܒܫܒܐ","ܬܠܬܐ ܒܫܒܐ","ܐܪܒܥܐ ܒܫܒܐ","ܚܡܫܐ ܒܫܒܐ","ܥܪܘܒܬܐ","ܫܒܬܐ"],
				namesAbbr: ["\u070fܐ \u070fܒܫ","\u070fܒ \u070fܒܫ","\u070fܓ \u070fܒܫ","\u070fܕ \u070fܒܫ","\u070fܗ \u070fܒܫ","\u070fܥܪܘܒ","\u070fܫܒ"],
				namesShort: ["ܐ","ܒ","ܓ","ܕ","ܗ","ܥ","ܫ"]
			},
			months: {
				names: ["ܟܢܘܢ ܐܚܪܝ","ܫܒܛ","ܐܕܪ","ܢܝܣܢ","ܐܝܪ","ܚܙܝܪܢ","ܬܡܘܙ","ܐܒ","ܐܝܠܘܠ","ܬܫܪܝ ܩܕܝܡ","ܬܫܪܝ ܐܚܪܝ","ܟܢܘܢ ܩܕܝܡ",""],
				namesAbbr: ["\u070fܟܢ \u070fܒ","ܫܒܛ","ܐܕܪ","ܢܝܣܢ","ܐܝܪ","ܚܙܝܪܢ","ܬܡܘܙ","ܐܒ","ܐܝܠܘܠ","\u070fܬܫ \u070fܐ","\u070fܬܫ \u070fܒ","\u070fܟܢ \u070fܐ",""]
			},
			AM: ["ܩ.ܛ","ܩ.ܛ","ܩ.ܛ"],
			PM: ["ܒ.ܛ","ܒ.ܛ","ܒ.ܛ"],
			patterns: {
				d: "dd/MM/yyyy",
				D: "dd MMMM, yyyy",
				t: "hh:mm tt",
				T: "hh:mm:ss tt",
				f: "dd MMMM, yyyy hh:mm tt",
				F: "dd MMMM, yyyy hh:mm:ss tt",
				M: "dd MMMM"
			}
		}
	}
});

}( this ));
