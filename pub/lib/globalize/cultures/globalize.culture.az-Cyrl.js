/**
 * Globalize Culture az-Cyrl
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

Globalize.addCultureInfo( "az-Cyrl", "default", {
	name: "az-Cyrl",
	englishName: "Azeri (Cyrillic)",
	nativeName: "Азәрбајҹан дили",
	language: "az-Cyrl",
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
			",": " ",
			".": ",",
			symbol: "ман."
		}
	},
	calendars: {
		standard: {
			"/": ".",
			firstDay: 1,
			days: {
				names: ["Базар","Базар ертәси","Чәршәнбә ахшамы","Чәршәнбә","Ҹүмә ахшамы","Ҹүмә","Шәнбә"],
				namesAbbr: ["Б","Бе","Ча","Ч","Ҹа","Ҹ","Ш"],
				namesShort: ["Б","Бе","Ча","Ч","Ҹа","Ҹ","Ш"]
			},
			months: {
				names: ["Јанвар","Феврал","Март","Апрел","Мај","Ијун","Ијул","Август","Сентјабр","Октјабр","Нојабр","Декабр",""],
				namesAbbr: ["Јан","Фев","Мар","Апр","Мај","Ијун","Ијул","Авг","Сен","Окт","Ноя","Дек",""]
			},
			monthsGenitive: {
				names: ["јанвар","феврал","март","апрел","мај","ијун","ијул","август","сентјабр","октјабр","нојабр","декабр",""],
				namesAbbr: ["Јан","Фев","Мар","Апр","мая","ијун","ијул","Авг","Сен","Окт","Ноя","Дек",""]
			},
			AM: null,
			PM: null,
			patterns: {
				d: "dd.MM.yyyy",
				D: "d MMMM yyyy",
				t: "H:mm",
				T: "H:mm:ss",
				f: "d MMMM yyyy H:mm",
				F: "d MMMM yyyy H:mm:ss",
				M: "d MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
