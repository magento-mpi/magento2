/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

// use Jquery variable instead of $
// to avoid naming conflicts with prototype and to keep ability to make unit tests
jQuery(window).on('load', function() {
    verifyfields();
    jQuery("#unit_price_use, #unit_price_unit, #unit_price_amount, #unit_price_base_unit, #unit_price_base_amount")
    .on('change', verifyfields);
});

function verifyfields(){
    var basePriceUse = jQuery('#unit_price_use');
    var basePriceUnit = jQuery('#unit_price_unit');
    var basePriceAmount = jQuery('#unit_price_amount');
    var basePriceBaseUnit = jQuery('#unit_price_base_unit');
    var basePriceBaseAmount = jQuery('#unit_price_base_amount');

    if (basePriceUse) {
        if (basePriceUse.val() == '1') {
            basePriceUnit.parents('tr').show();
            basePriceAmount.parents('tr').show();
            basePriceAmount.addClass('required-entry')
                .addClass('validate-greater-than-zero');
            basePriceBaseUnit.parents('tr').show();
            basePriceBaseAmount.parents('tr').show();
            basePriceBaseAmount.addClass('validate-greater-than-zero');
        } else {
            basePriceUnit.parents('tr').hide();

            basePriceAmount.parents('tr').hide();
            basePriceAmount.removeClass('required-entry')
                .removeClass('validate-greater-than-zero');
            basePriceBaseUnit.parents('tr').hide();
            basePriceBaseAmount.parents('tr').hide();
            basePriceBaseAmount.removeClass('validate-greater-than-zero');
        }
        if(basePriceUnit.val() === 'KG' && jQuery('#weight')) {
            basePriceAmount.val(jQuery('#weight').val());
            basePriceAmount.attr('readonly', 'readonly');
        } else {
            basePriceAmount.removeAttr('readonly');
        }
    }
}

