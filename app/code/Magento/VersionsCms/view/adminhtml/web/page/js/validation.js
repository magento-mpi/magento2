/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
define([
	"jquery",
	"jquery/ui",
	"mage/validation"
], function($){
    "use strict";
    
    $.widget("mage.validation", $.mage.validation, {
        options: {
            ignore: 'form form input, form form select, form form textarea'
        }
    });

});