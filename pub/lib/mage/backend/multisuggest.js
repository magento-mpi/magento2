/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/
(function($) {
    'use strict';
    $.widget('mage.multisuggest', $.mage.suggest, {
        _createHiddenInput: function(){
            return $('<select/>', {
                type: 'hidden',
                name: this.element.attr('name')
            })
        }
    });
})(jQuery);