/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PageTest = TestCase('DesignEditor_PageTest');
PageTest.prototype.testInit = function() {
    var page = jQuery(window).vde_page();
    assertEquals(true, page.is(':vde-vde_page'));
    page.vde_page('destroy');
};
PageTest.prototype.testDefaultOptions = function() {
    var page = jQuery(window).vde_page();
    assertEquals('iframe#vde_container_frame', page.vde_page('option', 'frameSelector'));
    assertEquals('.vde_element_wrapper.vde_container', page.vde_page('option', 'containerSelector'));
    assertEquals('#vde_toolbar_row', page.vde_page('option', 'panelSelector'));
    assertEquals('.vde_element_wrapper', page.vde_page('option', 'highlightElementSelector'));
    assertEquals('.vde_element_title', page.vde_page('option', 'highlightElementTitleSelector'));
    assertEquals('#vde_highlighting', page.vde_page('option', 'highlightCheckboxSelector'));
    page.vde_page('destroy');
};
PageTest.prototype.testInitHighlighting = function() {
    /*:DOC += <div id="vde_toolbar_row"><div id="vde_highlighting"></div></div> */
    var page = jQuery(window).vde_page();
    var highlightCheckboxSelector = page.vde_page('option', 'highlightCheckboxSelector');
    assertEquals(true, jQuery(highlightCheckboxSelector).is(':vde-vde_checkbox'));
    page.vde_page('destroy');
};
PageTest.prototype.testDestroy = function() {
    /*:DOC +=
     <div id="vde_toolbar_row"></div>
     <div class="vde_history_toolbar"></div>
     <div class="vde_element_wrapper vde_container"></div>
     */

    jQuery(window).vde_page();
    jQuery(window).vde_page('destroy');

    //check no garbage is left
    assertFalse($('#vde_toolbar_row').is(':vde-vde_panel'));
    assertFalse($('.vde_history_toolbar').is(':vde-vde_historyToolbar'));
    assertFalse($(window).is(':vde-vde_history'));
    assertFalse($('.vde_element_wrapper').is(':vde-vde_removable'));
    assertFalse($('.vde_element_wrapper.vde_container').is(':vde-vde_container'));
};

