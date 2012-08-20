/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
HistoryToolbarTest = TestCase('HistoryToolbarTest');
HistoryToolbarTest.prototype.testInit = function() {
    /*:DOC += <div class="vde_history_toolbar"></div> */
    var container = jQuery('.vde_history_toolbar').vde_historyToolbar();
    assertEquals(true, container.is(':vde-vde_historyToolbar'));
    container.vde_historyToolbar('destroy');
};
