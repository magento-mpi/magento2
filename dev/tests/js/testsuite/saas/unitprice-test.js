/**
 * {license_notice}
 *
 * @category    unitprice.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
UnitPriceTest = TestCase('UnitPriceTest');
UnitPriceTest.prototype.testUnitPrice = function() {
    /*:DOC +=
    <input id="weight" value="5.0000">
    <table>
        <tr>
            <td class="value">
                <select id="unit_price_use">
                    <option selected="selected" value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </td>
        </tr>
        <tr style="display: none;">
            <td class="value">
                <select id="unit_price_unit" class=" select">
                    <option selected="selected" value="LBS">LBS</option>
                    <option value="KG">KG</option>
                </select>
            </td>
        </tr>
        <tr style="display: none;">
            <td class="value">
                <input id="unit_price_amount" class="input-text" value="5.0000" readonly="readonly"/>
            </td>
        </tr>
        <tr style="display: none;">
            <td class="value">
                <select id="unit_price_base_unit" class=" select"/>
            </td>
        </tr>
        <tr style="display: none;">
            <td class="value">
                <input id="unit_price_base_amount" class="input-text"/>
            </td>
        </tr>
    </table>
    */

    var basePriceUse = $('#unit_price_use');
    var basePriceUnit = $('#unit_price_unit');
    var basePriceAmount = $('#unit_price_amount');
    var basePriceAmountBaseAmount = $('#unit_price_amount, #unit_price_base_amount');
    var baseAllElements = $('#unit_price_unit, #unit_price_amount, #unit_price_base_unit, #unit_price_base_amount');

    assertTrue(baseAllElements.is(':hidden'));
    assertFalse(basePriceAmount.hasClass('required-entry'));
    assertFalse(basePriceAmountBaseAmount.hasClass('validate-greater-than-zero'));

    basePriceUse.val(1);
    assertFalse(baseAllElements.is(':visible'));
    assertFalse(basePriceAmount.hasClass('required-entry'));
    assertFalse(basePriceAmountBaseAmount.hasClass('validate-greater-than-zero'));
    assertTrue(basePriceAmount.prop('readonly'));

    basePriceUnit.val('KG');
    assertTrue(basePriceAmount.prop('readonly'));
    assertEquals(basePriceAmount.val(), $('#weight').val());
};
