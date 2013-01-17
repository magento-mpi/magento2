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
         * @return {Element}
         * @private
         */
        _createValueField: function() {
            return $('<select/>', {
                type: 'hidden',
                multiple: 'multiple'
            });
        },

        /**
         * @override
         * @private
         */
        _create: function() {
            this._super();
            this.valueField.hide();
        }
    });

    $.widget('mage.multisuggest', $.mage.multisuggest, {
        options: {
            multiSuggestWrapper: '<ul class="category-selector-choices"><li class="category-selector-search-field">' +
                '</li></ul>',
            choiceTemplate: '<li class="category-selector-search-choice button"><div>${text}</div>' +
                '<span class="category-selector-search-choice-close" tabindex="-1"></span></li>'
        },

        /**
         * @override
         * @private
         */
        _render: function() {
            this._super();
            this.element.wrap(this.options.multiSuggestWrapper);
            this.elementWrapper = this.element.parent();
        },

        /**
         *
         * @param {Object} item object with selected item data
         * @private
         */
        _renderChoice: function(item) {
            $.tmpl(this.options.choiceTemplate, {text: item.label})
                .data(item)
                .insertBefore(this.elementWrapper)
                .find('.category-selector-search-choice-close')
                .on('click', function(){$(this).parent().remove();});
        }
    });
})(jQuery);
