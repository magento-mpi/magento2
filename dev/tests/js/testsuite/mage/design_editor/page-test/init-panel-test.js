/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PageTestInitPanel = TestCase('DesignEditor_PageTest_InitPanel');
PageTestInitPanel.prototype.testInitPanel = function() {
    /*:DOC +=
        <div id="vde_toolbar_row"></div>
        <iframe name="vde_container_frame" id="vde_container_frame" class="vde_container_frame"></iframe>
    */
    var page = jQuery(window).vde_page();
    var frameSelector = page.vde_page('option', 'frameSelector');
    jQuery(frameSelector).triggerHandler('load');
    var panelSelector = page.vde_page('option', 'panelSelector');
    assertEquals(true, jQuery(panelSelector).is(':vde-vde_panel'));
    page.vde_page('destroy');
};
