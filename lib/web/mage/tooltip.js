/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    "jquery",
    "jquery/ui"
], function($){
	'use strict';
	
	//Widget Wrapper
	$.widget('mage.tooltip', $.ui.tooltip, {
	});

    return $.mage.tooltip;
});
