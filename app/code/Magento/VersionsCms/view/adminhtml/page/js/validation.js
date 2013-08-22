/**
 * {license_notice}
 *
 * @category    design
 * @package     default_default
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget("mage.validation", $.mage.validation, {
        options: {
            ignore: 'form form input, form form select, form form textarea'
        }
    });
})(jQuery);
