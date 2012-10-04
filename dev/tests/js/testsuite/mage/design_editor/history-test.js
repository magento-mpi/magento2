/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
HistoryTest = TestCase('HistoryTest');
HistoryTest.prototype.testInit = function() {
    jQuery(window).vde_history();
    assertEquals(true, jQuery(window).is(':vde-vde_history'));
    jQuery(window).vde_history('destroy');
};
