/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */

TreeTest = TestCase('DesignEditor_TreeTest');
TreeTest.prototype.testInit = function() {
    /*:DOC += <div id="tree"></div> */
    var tree = jQuery('#tree').vde_tree();
    assertEquals(true, tree.is(':vde-vde_tree'));
    tree.vde_tree('destroy');
};
TreeTest.prototype.testDefaultOptions = function() {
    /*:DOC += <div id="tree"></div> */
    var tree = jQuery('#tree').vde_tree();
    var ui = jQuery('#tree').vde_tree('option', 'ui');
    var themes = jQuery('#tree').vde_tree('option', 'themes');
    assertEquals(1, ui.select_limit);
    assertEquals(false, ui.selected_parent_close);
    assertEquals(false, themes.dots);
    assertEquals(false, themes.icons);
    tree.vde_tree('destroy');
};
var TreeTestAsync = AsyncTestCase('DesignEditor_TreeTestAsync');
TreeTestAsync.prototype.testTreeLoadWithInitialSelect = function(queue) {
    /*:DOC +=
     <div id="tree">
        <ul>
            <li rel="tree_element"><a href="#">All Pages</a></li>
        </ul>
     </div>
     */
    var tree = jQuery('#tree').data('selected', 'li[rel="tree_element"]');
    var selectNodeEventIsTriggered = false;
    var treeLoaded = false;
    queue.call('Step 1: Bind callback on "select_node" event and initialize tree widget', function(callbacks) {
        var selectNodeEventTriggered = callbacks.add(function() { selectNodeEventIsTriggered = true; });
        tree
            .on('select_node.jstree', function() {
                selectNodeEventTriggered();
            })
            .on('loaded.jstree', function() {
                treeLoaded = true;
            });
        tree.vde_tree();
    });
    queue.call('Step 2: Check if "select_node" event is triggered', function() {
        assertEquals(true, treeLoaded);
        assertEquals(true, selectNodeEventIsTriggered);
        tree.vde_tree('destroy');
    });
};
TreeTestAsync.prototype.testTreeLoadWithoutInitialSelect = function(queue) {
    /*:DOC +=
     <div id="tree">
        <ul>
            <li rel="tree_element"><a href="#">All Pages</a></li>
        </ul>
     </div>
     */
    var tree = jQuery('#tree');
    var selectNodeEventIsTriggered = false;
    queue.call('Step 1: Bind callback on "select_node" event and initialize tree widget', function(callbacks) {
        var treeLoaded = callbacks.add(function() {});
        tree
            .on('select_node.jstree', function() {
                selectNodeEventIsTriggered = true;
            })
            .on('loaded.jstree', function() {
                treeLoaded();
            });
        tree.vde_tree();
    });
    queue.call('Step 2: Check if "select_node" event is triggered', function() {
        assertEquals(false, selectNodeEventIsTriggered);
        tree.vde_tree('destroy');
    });
};
TreeTestAsync.prototype.testTreeSelectNodeOnLoad = function(queue) {
    /*:DOC +=
     <div id="tree">
        <ul>
            <li rel="tree_element"><a href="#link">All Pages</a></li>
        </ul>
     </div>
     */
    var tree = jQuery('#tree').data('selected', 'li[rel="tree_element"]');
    var linkSelectedEventIsTriggered = false;
    var locationIsChanged = false;
    queue.call('Step 1: Bind callback on "select_node" event and initialize tree widget', function(callbacks) {
        var linkSelected = callbacks.add(function(url) {
            locationIsChanged = window.location.hash == url;
        });
        tree
            .on('link_selected.vde_tree', function() {
                linkSelectedEventIsTriggered = true;
            })
            .on('select_node.jstree', function(e, data) {
                linkSelected(jQuery(data.rslt.obj).find('a:first').attr('href'));
            });
        tree.vde_tree();
    });
    queue.call('Step 2: Check if "select_node" event is triggered', function() {
        assertEquals(true, linkSelectedEventIsTriggered);
        assertEquals(false, locationIsChanged);
        tree.vde_tree('destroy');
    });
};
TreeTestAsync.prototype.testTreeSelectNode = function(queue) {
    /*:DOC +=
     <div id="tree">
        <ul>
            <li rel="tree_element"><a href="#link">All Pages</a></li>
        </ul>
     </div>
     <iframe name="vde_container_frame" id="vde_container_frame" class="vde_container_frame"></iframe>
     */
    var tree = jQuery('#tree');
    var linkSelectedEventIsTriggered = false;
    var locationIsChanged = false;
    queue.call('Step 1: Bind callback on "link_selected" event and initialize tree widget', function(callbacks) {
        var nodeSelected = callbacks.add(function(url) {
            locationIsChanged = $('.vde_container_frame').attr('src') == url;
        });
        tree
            .on('loaded.jstree', function() {
                tree.on('select_node.jstree', function(e, data) {
                    nodeSelected(jQuery(data.rslt.obj).find('a:first').attr('href'));
                });
                jQuery('li[rel="tree_element"] a').trigger('click');
            })
            .on('link_selected.vde_tree', function() {
                linkSelectedEventIsTriggered = true;
            });
        tree.vde_tree();
    });
    queue.call('Step 2: Check if "link_selected" event is triggered', function() {
        assertEquals(true, linkSelectedEventIsTriggered);
        assertEquals(true, locationIsChanged);
        tree.vde_tree('destroy');
    });
};
