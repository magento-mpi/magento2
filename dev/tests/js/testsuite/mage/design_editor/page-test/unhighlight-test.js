/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PageTestUnhighlight = TestCase('DesignEditor_PageTest_Unhighlight');
PageTestUnhighlight.prototype.testUnhighlight = function() {
    /*:DOC += <iframe name="vde_container_frame" id="vde_container_frame" class="vde_container_frame"></iframe> */
    /*:DOC iframeContent =
        <div>
            <div id="vde_element_1" class="vde_element_wrapper vde_container">
                <div class="vde_element_title">Title 1</div>
                <div id="vde_element_2" class="vde_element_wrapper vde_draggable">
                    <div class="vde_element_title">Title 2</div>
                    <div class="block block-list block-compare" id="block">
                        <div class="block-title">
                            <strong><span>Block Title</span></strong>
                        </div>
                        <div class="block-content">
                            <p class="empty">Block Content</p>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    */

    jQuery.fx.off = true;
    var page = jQuery(window).vde_page();

    var frameSelector = page.vde_page('option', 'frameSelector');

    jQuery(frameSelector).triggerHandler('load');
    jQuery(frameSelector).contents().find("body:first").html(this.iframeContent);

    var highlightElementTitleSelector = page.vde_page('option', 'highlightElementTitleSelector');
    var highlightElementSelector = page.vde_page('option', 'highlightElementSelector');
    assertEquals(true, jQuery(frameSelector).contents().find(highlightElementSelector).size() > 0);
    var hierarchy = {};
    jQuery(frameSelector).contents().find(highlightElementSelector).each(function() {
        var elem = jQuery(this);
        hierarchy[elem.attr('id')] = elem.contents(':not(' + highlightElementTitleSelector + ')');
    });
    page.vde_page('destroy');
    page = jQuery(window).vde_page();
    jQuery(frameSelector).triggerHandler('load');
    page.trigger('unchecked.vde_checkbox');
    var hierarchyIsCorrect = null;
    jQuery.each(hierarchy, function(parentKey, parentVal) {
        jQuery.each(parentVal, function() {
            hierarchyIsCorrect = !jQuery(this).parents('#' + parentKey).size();
        })
    });
    assertEquals(true, hierarchyIsCorrect);
    assertEquals(false, jQuery('.vde_wrapper_hidden').is(':visible'));
    assertEquals(false, jQuery(highlightElementTitleSelector).is(':visible'));
    page.vde_page('destroy');
    jQuery.fx.off = false;
};
