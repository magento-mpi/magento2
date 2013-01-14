/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint evil:true */
// Temporary solution, will be replaced when plug-in "mage" will be merged to master
(function($){
	"use strict";
	var bootstrap = function() {
		$('[data-mage-init]').each(function(){
			var inits = $(this).data('mage-init') || {};
			// in case it's not well-formed JSON inside data attribute, evaluate it manually
			if (typeof inits === 'string') {
				try {
					inits = eval('(' + inits + ')');
				} catch (e) {
					inits = {};
				}
			}
			$.each(inits, $.proxy(function(key, args){
				$(this)[key].apply($(this), $.makeArray(args));
			}, this));
		});
	};
	$(document).ready(bootstrap);
})(jQuery);
