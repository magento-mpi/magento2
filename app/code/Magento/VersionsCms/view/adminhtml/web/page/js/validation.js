/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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