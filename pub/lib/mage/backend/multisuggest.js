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
        _create: function() {
            this._super();
            this.valueField.hide();
        },

        /**
         * @override
         */
        _createValueField: function() {
            return $('<select/>', {
                type: 'hidden',
                multiple: 'multiple'
            });
        },

        /**
         * @override
         */
        _selectItem: function() {
            if (this.isDropdownShown() && this._focused) {
                this._selectedItem = this._readItemData(this._focused);
                if (this.valueField.find('option[value=' + this._selectedItem.value + ']').length) {
                    this._selectedItem = this._nonSelectedItem;
                }
                if (this._selectedItem !== this._nonSelectedItem) {
                    this._term = '';
                    this.valueField.append(
                        '<option value="' + this._selectedItem.value + '" selected="selected">' +
                            this._selectedItem.label + '</option>'
                    );
                }
            }
        },

        /**
         * @override
         */
        _hideDropdown: function() {
            this.element.val('');
            this.dropdown.hide().empty();
        }
    });

    $.widget('mage.multisuggest', $.mage.multisuggest, {
        options: {
            multiSuggestWrapper: '<ul class="category-selector-choices">' +
                '<li class="category-selector-search-field"></li></ul>',
            choiceTemplate: '<li class="category-selector-search-choice button"><div>${text}</div>' +
                '<span class="category-selector-search-choice-close" tabindex="-1" ' +
                'data-mage-init="{&quot;actionLink&quot;:{&quot;event&quot;:&quot;removeOption&quot;}}"></span></li>'
        },

        /**
         * @override
         */
        _render: function() {
            this._super();
            this.element.wrap(this.options.multiSuggestWrapper);
            this.elementWrapper = this.element.parent();

        },

        /**
         * @override
         */
        _selectItem: function() {
            this._superApply(arguments);
            if (this._selectedItem !== this._nonSelectedItem) {
                this._renderOption(this._selectedItem);
            }
        },

        /**
         * Render visual element of selected item
         * @param {Object} item - selected item
         * @private
         */
        _renderOption: function(item) {
            $.tmpl(this.options.choiceTemplate, {text: item.label})
                .data(item)
                .insertBefore(this.elementWrapper)
                .trigger('contentUpdated')
                .on('removeOption', $.proxy(function(e) {
                    this.valueField.find('option[value=' + $(e.currentTarget).data().value + ']').remove();
                    $(e.currentTarget).remove();
                }, this));
        }
    });
})(jQuery);
