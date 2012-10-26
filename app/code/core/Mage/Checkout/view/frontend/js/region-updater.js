/**
 * {license_notice}
 *
 * @category    frontend Checkout region-updater
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function($) {
    $.widget('mage.regionUpdater', {
        options: {
            regionTemplate: '<option value="${value}" title="${title}" {{if isSelected}}selected="selected"{{/if}}>${title}</option>'
        },
        _create: function() {
            this._updateRegion(this.element.find('option:selected').val());
            this.element.on('change', $.proxy(function(e) {
                this._updateRegion($(e.target).val());
            }, this));
            // Form validation
            this.element.addClass('required');
            $(this.options.formId).mage().validate();
            $(this.options.formId + ' button').on('click', $.proxy(function() {
                $(this.options.formId).submit();
            }, this));
        },
        _removeSelectOptions: function(selectElement) {
            selectElement.find('option').each(function (index){
                index && $(this).remove();
            });
        },
        _renderSelectOption: function(selectElement, key, value) {
            selectElement.append($.proxy(function() {
                $.template('regionTemplate', this.options.regionTemplate);
                if (this.options.defaultRegion === key) {
                    return $.tmpl('regionTemplate', {value: key, title: value.name, isSelected: true});
                } else {
                    return $.tmpl('regionTemplate', {value: key, title: value.name});
                }
            }, this));
        },
        _updateRegion: function(country) {
            // Clear validation error messages
            var form = $(this.options.formId),
                regionList = $(this.options.regionListId),
                regionInput = $(this.options.regionInputId),
                postcode = $(this.options.postcodeId);
            form.find('div.mage-error').remove();
            form.find('.mage-error').removeClass('mage-error');
            // Populate state/province dropdown list if available or use input box
            if (this.options.regionJson[country]) {
                this._removeSelectOptions(regionList);
                $.each(this.options.regionJson[country], $.proxy(function(key, value) {
                    this._renderSelectOption(regionList, key, value);
                }, this));
                regionList.addClass('required').show();
                regionInput.removeClass('required').hide();
            } else {
                regionList.removeClass('required').hide();
                regionInput.addClass('required').show();
            }
            // If country is in optionalzip list, make postcode input not required
            $.inArray(country, this.options.countriesWithOptionalZip) >= 0 ?
                regionList.add(regionInput).add(postcode).removeClass('required') :
                regionList.add(regionInput).add(postcode).addClass('required');
            // Add defaultvalue attribute to state/province select element
            regionList.attr('defaultvalue', this.options.defaultRegion);
        }
    });
}(jQuery));