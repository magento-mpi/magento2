/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    TypeSwitcher = function (data) {
        this._data = data;
        this._data.$weight = $('#' + data.weight_id);
        this._data.$weightContainer = $('#attribute-' + data.weight_id + '-container');
    };
    $.extend(TypeSwitcher.prototype, {
        attributesContainer:null,
        //Called after DOM is ready
        bindAll: function () {
            var attrContainer = this._getAttributesContainers(this._data.attributes),
                currentType = this._data.current_type,
                $weight = this._data.$weight,
                tabItem = $('#' + this._data.tab_id).parent();
            //initial attribute hiding
            $.each(this._data.attributes, function (id, applyTo) {
                if (applyTo.length === 0) {
                    return true; //continue
                }
                if (($.inArray(currentType, applyTo) === -1)) {
                    //Hide attribute if it's not applied to current type
                    attrContainer[id].css('display', 'none')
                        .find('select, input, textarea').addClass('validation-disabled');
                }
                attrContainer[id].addClass('element-container');
            });

            //Hide Downloadable Information tab for simple
            if (currentType === 'simple') {
                tabItem.css('display', 'none');
            } else {
                $weight.attr('disabled', 'disabled').addClass('validation-disabled');
            }

            this._checkbox = $('#' + this._data.is_virtual_id);
            this._checkbox.bind('click', {_this: this, tabItem: tabItem}, this._switcher);
        },
        //all attributes selectors, used for hiding and showing
        _getAttributesContainers: function (data) {
            if (this.attributesContainer === null) {
                var attributes = {};
                $.each(data, function (id, mask) {
                    attributes[id] = $('#attribute-' + id + '-container');
                });
                this.attributesContainer = attributes;
            }
            return this.attributesContainer;
        },
        //Showing and hiding attributes by Is Virtual switcher
        _switcher: function (event) {
            var attrContainer = event.data._this._getAttributesContainers(),
                attributes = event.data._this._data.attributes,
                $weight = event.data._this._data.$weight;
            if ('checked' === $(this).attr('checked')) {
                event.data.tabItem.css('display', '');
                $weight.attr('disabled', 'disabled');
                $.each(attributes, function (id, applyTo) {
                    if (applyTo.length === 0) {
                        return true; //continue
                    }

                    if ($.inArray('virtual', applyTo) !== -1) {
                        attrContainer[id].css('display', '')
                            .find('select, input, textarea').removeClass('validation-disabled');
                        $weight.addClass('validation-disabled');
                    } else {
                        attrContainer[id].css('display', 'none')
                            .find('select, input, textarea').addClass('validation-disabled');
                    }
                });
            } else {
                event.data.tabItem.css('display', 'none');
                $weight.removeAttr('disabled');
                $.each(attributes, function (id, applyTo) {
                    if (applyTo.length === 0) {
                        return true; //continue
                    }
                    if ($.inArray('simple', applyTo) === -1) {
                        attrContainer[id].css('display', 'none')
                            .find('select, input, textarea').addClass('validation-disabled');
                        $weight.removeClass('validation-disabled');
                    } else {
                        attrContainer[id].css('display', '')
                            .find('select, input, textarea').addClass('validation-disabled');
                    }
                });
            }
        }
    });
})(jQuery);