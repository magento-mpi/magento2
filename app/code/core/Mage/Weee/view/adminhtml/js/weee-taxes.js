/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    $.widget('mage.weeeTaxes', {
        _create: function () {
            var widget = this;
            this._initOptionItem();
            if ($('#price_type').val() === '0') {
                this.element.hide();
            }
            $.each(this.options.itemsData, function(){
                widget.addItem(this);
            })
        },
        _initOptionItem: function () {
            var widgetOptions = this.options;
            this._on({
                //Add new tax item
                'click [id$=add_new_tax_item]': function (event) {
                    this.addItem(event);
                },
                //Delete tax item
                'click button[id*=_delete_row_]': function (event) {
                    var parent = $(event.target).closest('tr');
                    parent.find('[id^="' + widgetOptions.prefix + '_weee_tax_row_"][id$="_delete"]').val(1);
                    parent.addClass('ignore-validate').hide();
                },
                //Change tax item country/state
                'change select[name$="[country]"]': function (event, data) {
                    var element = event.target || event.srcElement || event.currentTarget;
                    data = data || {};
                    if (typeof element !== 'undefined') {
                        data.prefix = widgetOptions.prefix;
                        data.index = $(event.target).closest('tr').attr('id').replace(data.prefix + '_weee_tax_row_', '');
                    }
                    var updater = new RegionUpdater(
                        data.prefix + '_weee_tax_row_' + data.index + '_country', null,
                        data.prefix + '_weee_tax_row_' + data.index + '_state', widgetOptions.region, 'disable', true
                    );
                    updater.update();
                    //set selected state value if set
                    if (data.state) {
                        $('#' + data.prefix + '_weee_tax_row_' + data.index + '_state').val(data.state);
                    }
                }
            });
            $('#price_type').on('change', function (event) {
                var attributeContainer = $('#attribute-' + widgetOptions.prefix + '-container'),
                    attributeItems = attributeContainer.find('[id^="' + widgetOptions.prefix + '_weee_tax_row_"][id$="_delete"]');
                if ($(event.target).val() === '0') {
                    attributeContainer.hide();
                    attributeItems.each(function () {
                        $(this).val(1);
                    })
                } else {
                    attributeContainer.show();
                    attributeItems.each(function () {
                        if ($(this).closest('tr').is(':visible')) {
                            $(this).val(0);
                        }
                    })
                }
            })
        },
        //Add custom option
        addItem: function (event) {
            var data = {},
                element = event.target || event.srcElement || event.currentTarget;
            if (typeof element !== 'undefined') {
                data.prefix = this.options.prefix;
                data.website_id = 0;
            } else {
                data = event;
            }
            data.index = this.options.itemCount++;
            this.element.find('#wee-tax-row-template').tmpl(data).appendTo(this.element.find('#' + data.prefix + '_container'));
            //set selected website_id value if set
            if (data.website_id) {
                $('#' + data.prefix + '_weee_tax_row_' + data.index + '_website').val(data.website_id);
            }
            //set selected country value if set
            if (data.country) {
                $('#' + data.prefix + '_weee_tax_row_' + data.index + '_country').val(data.country).trigger('change', data);
            }
        }
    });
})(jQuery);
