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
        /**
         * @override
         */
        _createValueField: function() {
            return $('<select/>', {
                type: 'hidden'
            });
        },

        /**
         * @override
         */
        _create: function() {
            this._super();
            this.valueField.hide();
        }
    });
})(jQuery);
