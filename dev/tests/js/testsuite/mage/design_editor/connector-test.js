/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
ConnectorTest = TestCase('DesignEditor_ConnectorTest');
ConnectorTest.prototype.setUp = function() {
    /*:DOC += <div class="vde_history_toolbar"></div>
        <div class="vde_element_wrapper vde-vde_removable"></div>
        <div class="vde_element_wrapper vde_container"></div>
      */
    if (jQuery(window).data('vde_connector')) {
        jQuery(window).vde_connector('destroy');
    }
    this.connector = jQuery(window).vde_connector();
    this.connectorInstance = this.connector.data('vde_connector');
};
ConnectorTest.prototype.tearDown = function() {
    var vdeWidgets = ['vde_history', 'vde_connector'];
    jQuery.each(vdeWidgets, function(i, widgetName) {
        var instance = jQuery(window).data(widgetName);
        if (instance) {
            instance.destroy();
        }
    });
    jQuery(window).off();
};
ConnectorTest.prototype.testDefaultOptions = function() {
    assertEquals('.vde_element_wrapper.vde_container', this.connectorInstance.options.containerSelector);
    assertEquals('.vde_element_wrapper', this.connectorInstance.options.highlightElementSelector);
    assertEquals('.vde_element_title', this.connectorInstance.options.highlightElementTitleSelector);
    assertEquals('#vde_highlighting', this.connectorInstance.options.highlightCheckboxSelector);
    assertEquals('.vde_history_toolbar', this.connectorInstance.options.historyToolbarSelector);
};
ConnectorTest.prototype.testInitHistory = function() {
    this.connectorInstance._initHistory();
    assertNotNull(window.vdeHistoryObject);
    assertEquals(true, jQuery(window).is(':vde-vde_history'));
};
ConnectorTest.prototype.testInitHistoryToolbar = function() {
    var historyToolbar = jQuery('.vde_history_toolbar'),
        historyToolbarInstance = jQuery('.vde_history_toolbar').data('vde_historyToolbar');
    assertEquals(true, historyToolbar.is(':vde-vde_historyToolbar'));
    assertNotNull(historyToolbarInstance._history);
};
ConnectorTest.prototype.testInitRemoveOperation = function() {
    var containers = jQuery('.vde_element_wrapper');
    assertNotNull(containers.data('vde_removable').history);
};
ConnectorTest.prototype.testSetHistoryForContainers = function() {
    var containers = jQuery('.vde_element_wrapper.vde_container');
    assertNotNull(containers.vde_container('getHistory'));
};
ConnectorTest.prototype.testDestroy = function() {
    this.connector.vde_connector('destroy');

    //check no garbage is left
    assertFalse($('#vde_toolbar').is(':vde-vde_panel'));
    assertFalse($('.vde_history_toolbar').is(':vde-vde_historyToolbar'));
    assertFalse($(window).is(':vde-vde_history'));
    assertFalse($('.vde_element_wrapper').is(':vde-vde_removable'));
    assertFalse($('.vde_element_wrapper.vde_container').is(':vde-vde_container'));
};
