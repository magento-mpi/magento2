/**
 * Globalize Culture moh-CA
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

Globalize.addCultureInfo( "moh-CA", "default", {
	name: "moh-CA",
	englishName: "Mohawk (Mohawk)",
	nativeName: "Kanien'kéha",
	language: "moh",
	numberFormat: {
		groupSizes: [3,0],
		percent: {
			groupSizes: [3,0]
		}
	},
	calendars: {
		standard: {
			days: {
				names: ["Awentatokentì:ke","Awentataón'ke","Ratironhia'kehronòn:ke","Soséhne","Okaristiiáhne","Ronwaia'tanentaktonhne","Entákta"],
				namesShort: ["S","M","T","W","T","F","S"]
			},
			months: {
				names: ["Tsothohrkó:Wa","Enniska","Enniskó:Wa","Onerahtókha","Onerahtohkó:Wa","Ohiari:Ha","Ohiarihkó:Wa","Seskéha","Seskehkó:Wa","Kenténha","Kentenhkó:Wa","Tsothóhrha",""]
			}
		}
	}
});

}( this ));
