/**
 * Globalize Culture quz-BO
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

Globalize.addCultureInfo( "quz-BO", "default", {
	name: "quz-BO",
	englishName: "Quechua (Bolivia)",
	nativeName: "runasimi (Qullasuyu)",
	language: "quz",
	numberFormat: {
		",": ".",
		".": ",",
		percent: {
			pattern: ["-%n","%n"],
			",": ".",
			".": ","
		},
		currency: {
			pattern: ["($ n)","$ n"],
			",": ".",
			".": ",",
			symbol: "$b"
		}
	},
	calendars: {
		standard: {
			days: {
				names: ["intichaw","killachaw","atipachaw","quyllurchaw","Ch' askachaw","Illapachaw","k'uychichaw"],
				namesAbbr: ["int","kil","ati","quy","Ch'","Ill","k'u"],
				namesShort: ["d","k","a","m","h","b","k"]
			},
			months: {
				names: ["Qulla puquy","Hatun puquy","Pauqar waray","ayriwa","Aymuray","Inti raymi","Anta Sitwa","Qhapaq Sitwa","Uma raymi","Kantaray","Ayamarq'a","Kapaq Raymi",""],
				namesAbbr: ["Qul","Hat","Pau","ayr","Aym","Int","Ant","Qha","Uma","Kan","Aya","Kap",""]
			},
			AM: ["a.m.","a.m.","A.M."],
			PM: ["p.m.","p.m.","P.M."],
			patterns: {
				d: "dd/MM/yyyy",
				D: "dddd, dd' de 'MMMM' de 'yyyy",
				t: "hh:mm tt",
				T: "hh:mm:ss tt",
				f: "dddd, dd' de 'MMMM' de 'yyyy hh:mm tt",
				F: "dddd, dd' de 'MMMM' de 'yyyy hh:mm:ss tt",
				Y: "MMMM' de 'yyyy"
			}
		}
	}
});

}( this ));
