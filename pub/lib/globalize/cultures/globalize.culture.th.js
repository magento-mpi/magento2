/**
 * Globalize Culture th
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

Globalize.addCultureInfo( "th", "default", {
	name: "th",
	englishName: "Thai",
	nativeName: "ไทย",
	language: "th",
	numberFormat: {
		currency: {
			pattern: ["-$n","$n"],
			symbol: "฿"
		}
	},
	calendars: {
		standard: {
			name: "ThaiBuddhist",
			firstDay: 1,
			days: {
				names: ["อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์"],
				namesAbbr: ["อา.","จ.","อ.","พ.","พฤ.","ศ.","ส."],
				namesShort: ["อ","จ","อ","พ","พ","ศ","ส"]
			},
			months: {
				names: ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม",""],
				namesAbbr: ["ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.",""]
			},
			eras: [{"name":"พ.ศ.","start":null,"offset":-543}],
			twoDigitYearMax: 2572,
			patterns: {
				d: "d/M/yyyy",
				D: "d MMMM yyyy",
				t: "H:mm",
				T: "H:mm:ss",
				f: "d MMMM yyyy H:mm",
				F: "d MMMM yyyy H:mm:ss",
				M: "dd MMMM",
				Y: "MMMM yyyy"
			}
		},
		Gregorian_Localized: {
			firstDay: 1,
			days: {
				names: ["อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์"],
				namesAbbr: ["อา.","จ.","อ.","พ.","พฤ.","ศ.","ส."],
				namesShort: ["อ","จ","อ","พ","พ","ศ","ส"]
			},
			months: {
				names: ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม",""],
				namesAbbr: ["ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.",""]
			},
			patterns: {
				d: "d/M/yyyy",
				D: "'วัน'dddd'ที่' d MMMM yyyy",
				t: "H:mm",
				T: "H:mm:ss",
				f: "'วัน'dddd'ที่' d MMMM yyyy H:mm",
				F: "'วัน'dddd'ที่' d MMMM yyyy H:mm:ss",
				M: "dd MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
