/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PanelTest = TestCase('PanelTest');
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
