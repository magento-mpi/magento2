/**
 * Globalize Culture bn-BD
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

Globalize.addCultureInfo( "bn-BD", "default", {
	name: "bn-BD",
	englishName: "Bengali (Bangladesh)",
	nativeName: "বাংলা (বাংলাদেশ)",
	language: "bn",
	numberFormat: {
		groupSizes: [3,2],
		percent: {
			pattern: ["-%n","%n"],
			groupSizes: [3,2]
		},
		currency: {
			pattern: ["$ -n","$ n"],
			groupSizes: [3,2],
			symbol: "৳"
		}
	},
	calendars: {
		standard: {
			"/": "-",
			":": ".",
			firstDay: 1,
			days: {
				names: ["রবিবার","সোমবার","মঙ্গলবার","বুধবার","বৃহস্পতিবার","শুক্রবার","শনিবার"],
				namesAbbr: ["রবি.","সোম.","মঙ্গল.","বুধ.","বৃহস্পতি.","শুক্র.","শনি."],
				namesShort: ["র","স","ম","ব","ব","শ","শ"]
			},
			months: {
				names: ["জানুয়ারী","ফেব্রুয়ারী","মার্চ","এপ্রিল","মে","জুন","জুলাই","আগস্ট","সেপ্টেম্বর","অক্টোবর","নভেম্বর","ডিসেম্বর",""],
				namesAbbr: ["জানু.","ফেব্রু.","মার্চ","এপ্রিল","মে","জুন","জুলাই","আগ.","সেপ্টে.","অক্টো.","নভে.","ডিসে.",""]
			},
			AM: ["পুর্বাহ্ন","পুর্বাহ্ন","পুর্বাহ্ন"],
			PM: ["অপরাহ্ন","অপরাহ্ন","অপরাহ্ন"],
			patterns: {
				d: "dd-MM-yy",
				D: "dd MMMM yyyy",
				t: "HH.mm",
				T: "HH.mm.ss",
				f: "dd MMMM yyyy HH.mm",
				F: "dd MMMM yyyy HH.mm.ss",
				M: "dd MMMM"
			}
		}
	}
});

}( this ));
