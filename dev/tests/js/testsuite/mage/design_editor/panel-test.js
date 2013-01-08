/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PanelTest = TestCase('DesignEditor_PanelTest');
PanelTest.prototype.testInit = function() {
    /*:DOC += <div id="panel"></div> */
    var panel = jQuery('#panel').vde_panel();
    assertEquals(true, panel.is(':vde-vde_panel'));
    panel.vde_panel('destroy');
};
PanelTest.prototype.testDefaultOptions = function() {
    /*:DOC += <div id="panel"></div> */
    var panel = jQuery('#panel').vde_panel();
    assertEquals('.vde_toolbar_cell', panel.vde_panel('option', 'cellSelector'));
    assertEquals('#vde_handles_hierarchy', panel.vde_panel('option', 'handlesHierarchySelector'));
    assertEquals('#vde_handles_tree', panel.vde_panel('option', 'treeSelector'));
    panel.vde_panel('destroy');
};
PanelTest.prototype.testInitToolbalCell = function() {
    /*:DOC +=
    <div id="panel">
        <div class="vde_toolbar_cell">
             <div class="vde_toolbar_cell_title" />
             <div class="vde_toolbar_cell_content"  />
        </div>
    </div>
    */
    var panel = jQuery('#panel').vde_panel();
    var cellSelector = panel.vde_panel('option', 'cellSelector');
    assertEquals(true, panel.find(cellSelector).is(':vde-vde_menu'));
    panel.vde_panel('destroy');
};
PanelTest.prototype.testInitHandlesHierarchy = function() {
    /*:DOC +=
    <div id="panel">
        <div class="vde_toolbar_cell" id="vde_handles_hierarchy">
            <div class="vde_toolbar_cell_title" />
            <div class="vde_toolbar_cell_content"  />
        </div>
    </div>
    */
    var panel = jQuery('#panel').vde_panel();
    var handlesHierarchySelector = panel.vde_panel('option', 'handlesHierarchySelector');
    var treeSelector = panel.vde_panel('option', 'treeSelector');
    var handlesHierarchy = panel.find(handlesHierarchySelector);
    assertEquals(true, panel.find(handlesHierarchySelector).is(':vde-vde_menu'));
    assertEquals(treeSelector, handlesHierarchy.vde_menu('option', 'treeSelector'));
    assertEquals(true, handlesHierarchy.vde_menu('option', 'slimScroll'));
    panel.vde_panel('destroy');
};
PanelTest.prototype.testBind = function() {
    /*:DOC += <div id="panel"></div> */

    var panel = jQuery('#panel').vde_panel();

    var switchModeEventHandlers = $._data($('body').get(0), "events").switchMode;
    var expectedGuid = jQuery('#panel').data("vde_panel")._onSwitchMode.guid;
    var switchModeEventHandlerFound = false;
    for (var arrayIndex in switchModeEventHandlers) {
        if (typeof switchModeEventHandlers[arrayIndex] === 'object'
            && 'handler' in switchModeEventHandlers[arrayIndex]
            && typeof switchModeEventHandlers[arrayIndex].handler === 'function'
            && switchModeEventHandlers[arrayIndex].handler.guid == expectedGuid
        ) {
            switchModeEventHandlerFound = true;
        }
    }

    assertTrue(switchModeEventHandlerFound);

    panel.vde_panel('destroy');
};
PanelTest.prototype.testSaveTemporaryLayoutChanges = function() {
    /*:DOC +=
        <div id="panel"></div>
        <iframe name="vde_container_frame" id="vde_container_frame" class="vde_container_frame"></iframe>
    */
    /*:DOC iframeContent =
        <div>
            <div id="vde_element_1" class="vde_element_wrapper vde_container">
                <div class="vde_element_title">Title 1</div>
            </div>
        </div>
    */
    var page = jQuery(window).vde_page();
    var frameSelector = page.vde_page('option', 'frameSelector');
    jQuery(frameSelector).contents().find("body:first").html(this.iframeContent);

    var panel = jQuery('#panel').vde_panel({editorFrameSelector: frameSelector});
    var history = jQuery(window).vde_history();
    jQuery(frameSelector).get(0).contentWindow.vdeHistoryObject = history.data('vde_history');

    var saveChangesUrl = 'test_saveChangesUrl';
    var modeUrl = 'test_modeUrl';
    var historyItem = $.fn.changeFactory.getInstance('layout');
    historyItem.setData({
        action: 'move',
        block: 'test_block_name',
        origin: {
            container: 'test_origin_container',
            order: 'test_origin_position'
        },
        destination: {
            container: 'test_destination_container',
            order: 'test_destination_position'
        }
    });
    var testHandle = 'test_handle';
    jQuery(frameSelector).attr('src', 'http://m2.local/index.php/vde/design/page/type/handle/' + testHandle);

    var expectedPostData = 'theme_id=1&layoutUpdate%5B0%5D%5Bhandle%5D=current_handle&layoutUpdate%5B0%5D%5Btype%5D=layout&layoutUpdate%5B0%5D%5Belement_name%5D=test_block_name&layoutUpdate%5B0%5D%5Baction_name%5D=move&layoutUpdate%5B0%5D%5Bdestination_container%5D=test_destination_container&layoutUpdate%5B0%5D%5Bdestination_order%5D=test_destination_position&layoutUpdate%5B0%5D%5Borigin_container%5D=test_origin_container&layoutUpdate%5B0%5D%5Borigin_order%5D=test_origin_position&handle=test_handle';
    jQuery(frameSelector).get(0).contentWindow.vdeHistoryObject.addItem(historyItem);

    jQuery(document).on('ajaxSend', function(e, jqXHR, settings) {
        jqXHR.abort();
        assertEquals(expectedPostData, settings.data);
    });
    jQuery(window).on('beforeunload', function(e) {
        e.stopImmediatePropagation();
        assertEquals(modeUrl, document.location);
    });

    jQuery('#panel').data("vde_panel").saveTemporaryLayoutChanges(1, saveChangesUrl, modeUrl);

    page.vde_page('destroy');
    panel.vde_panel('destroy');
    history.vde_history('destroy');
};
