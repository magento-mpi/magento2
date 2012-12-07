/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PageTestHighlight = TestCase('DesignEditor_PageTest_Highlight');
PageTestHighlight.prototype.testHighlight = function() {
    /*:DOC +=
        <iframe name="vde_container_frame" id="vde_container_frame" class="vde_container_frame"></iframe>
    */
    /*:DOC iframeContent =
        <div>
            <div id="vde_element_1" class="vde_element_wrapper vde_container vde_wrapper_hidden">
                <div class="vde_element_title">Title 1</div>
            </div>
            <!--start_vde_element_1-->
            <div id="vde_element_2" class="vde_element_wrapper vde_draggable vde_wrapper_hidden">
                <div class="vde_element_title">Title 2</div>
            </div>
            <!--start_vde_element_2-->
            <div class="block block-list" id="block">
                <div class="block-title">
                    <strong><span>Block Title</span></strong>
                </div>
                <div class="block-content">
                    <p class="empty">Block Content</p>
                </div>
            </div>
            <!--end_vde_element_2-->
            <div id="vde_element_3" class="vde_element_wrapper vde_draggable vde_wrapper_hidden">
                <div class="vde_element_title">Title 3</div>
            </div>
            <!--end_vde_element_1-->
        </div>
    */

    jQuery.fx.off = true;
    var page = jQuery(window).vde_page();
    var frameSelector = page.vde_page('option', 'frameSelector');

    jQuery(frameSelector).triggerHandler('load');
    jQuery(frameSelector).contents().find("body:first").html(this.iframeContent);
    page.trigger('checked.vde_checkbox');
    var resultHierarchy = {
        vde_element_1: ['vde_element_2', 'vde_element_3'],
        vde_element_2: ['block']
    };
    var hierarchyIsCorrect = null;
    jQuery.each(resultHierarchy, function(parentKey, parentVal) {
        jQuery.each(parentVal, function(childKey, childVal) {
            hierarchyIsCorrect = !!jQuery(frameSelector).contents().find('#' + parentKey)
                .has(jQuery(frameSelector).contents().find('#' + childVal));
        })
    });
    assertEquals(true, hierarchyIsCorrect);
    assertEquals(true, jQuery(frameSelector).contents().find('.vde_wrapper_hidden').size() > 0);
    jQuery(frameSelector).contents().find('.vde_wrapper_hidden').each(function() {
        assertEquals(true, $(this).is(':visible'));
    });
    assertEquals(true,
        jQuery(frameSelector).contents().find(page.vde_page('option', 'highlightElementTitleSelector')).size() > 0
    );
    jQuery(frameSelector).contents().find(page.vde_page('option', 'highlightElementTitleSelector')).each(function() {
        assertEquals(true, $(this).is(':visible'));
    });

    page.vde_page('destroy');
    jQuery.fx.off = false;
};
