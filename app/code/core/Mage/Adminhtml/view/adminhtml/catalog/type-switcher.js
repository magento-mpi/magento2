/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    /**
     * Type Switcher
     *
     * @param {object} data
     * @constructor
     */
    var TypeSwitcher = function (data) {
        this._data = data;
        this.$type = $('#type_id');
        this.$weight = $('#' + data.weight_id);
        this.$is_virtual = $('#' + data.is_virtual_id);
        this.$tab = $('#' + data.tab_id);

        // @todo: move $is_virtual checkbox logic to separate widget
        if (this.$is_virtual.is(':checked')) {
            this.baseType = {
                virtual: this.$type.val(),
                real: 'simple'
            };
        } else {
            this.baseType = {
                virtual: 'virtual',
                real: this.$type.val()
            };
        }
    };
    $.extend(TypeSwitcher.prototype, {
        /** @lends {TypeSwitcher} */

        /**
         * Bind event
         */
        bindAll: function () {
            var self = this,
                $type = this.$type;
            $type.on('change', function() {
                self._switchToType($(this).val());
            });

            this.$is_virtual.on('change click', function() {
                if ($(this).is(':checked')) {
                    $type.val(self.baseType.virtual).trigger('change');
                    self.$weight.addClass('ignore-validate').attr('disabled', 'disabled');
                    self.$tab.show();
                } else {
                    $type.val(self.baseType.real).trigger('change');
                    self.$weight.removeClass('ignore-validate').removeAttr('disabled', 'disabled');
                    self.$tab.hide();
                }
            }).trigger('change');
        },

        /**
         * Get element bu code
         * @param {string} code
         * @return {jQuery|HTMLElement}
         */
        getElementByCode: function(code) {
            return $('#attribute-' + code + '-container');
        },

        /**
         * Show/hide elements based on type
         *
         * @param {string} typeCode
         * @private
         */
        _switchToType: function(typeCode) {
            var self = this,
                attributes = this._data.attributes;

            $.each(attributes, function(code, applyTo) {
                var attrContainer = self.getElementByCode(code);
                var $inputs = attrContainer.find('select, input, textarea');
                if (applyTo.length === 0 || $.inArray(typeCode, applyTo) !== -1) {
                    attrContainer.show();
                    $inputs.removeClass('ignore-validate');
                } else {
                    attrContainer.hide();
                    $inputs.addClass('ignore-validate');
                }
            });
        }
    });
    // export to global scope
    window.TypeSwitcher = TypeSwitcher;
})(jQuery);
