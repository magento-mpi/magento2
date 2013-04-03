/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PageTest = TestCase('DesignEditor_PageTest');
PageTest.prototype.setUp = function() {
    /*:DOC += <div id="vde_toolbar_row"><div id="vde_highlighting"></div></div>
        <div class="vde_history_toolbar"></div>
        <div class="vde_element_wrapper vde_container"></div>
     */
    this.page = jQuery(window).vde_page();
    this.pageInstance = this.page.data('vde_page');
};
PageTest.prototype.tearDown = function() {
    this.pageInstance.destroy();
};

PageTest.prototype.testInit = function() {
    assertEquals(true, this.page.is(':vde-vde_page'));
};
PageTest.prototype.testDefaultOptions = function() {
    assertEquals('iframe#vde_container_frame', this.pageInstance.options.frameSelector);
    assertEquals('.vde_element_wrapper.vde_container', this.pageInstance.options.containerSelector);
    assertEquals('#vde_toolbar_row', this.pageInstance.options.panelSelector);
    assertEquals('.vde_element_wrapper', this.pageInstance.options.highlightElementSelector);
    assertEquals('.vde_element_title', this.pageInstance.options.highlightElementTitleSelector);
    assertEquals('#vde_highlighting', this.pageInstance.options.highlightCheckboxSelector);
};
PageTest.prototype.testInitHighlighting = function() {
    var highlightCheckboxSelector = this.pageInstance.options.highlightCheckboxSelector;
    assertEquals(true, jQuery(highlightCheckboxSelector).is(':vde-vde_checkbox'));
};
PageTest.prototype.testDestroy = function() {
    this.pageInstance._initPanel();
    this.pageInstance.destroy();
    //check no garbage is left
    assertFalse($('#vde_toolbar_row').is(':vde-vde_panel'));
};

