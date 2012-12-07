/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PageTestProcessMarkers = TestCase('DesignEditor_PageTest_ProcessMarkers');
PageTestProcessMarkers.prototype.testProcessMarkers = function() {
    /*:DOC += <iframe name="vde_container_frame" id="vde_container_frame" class="vde_container_frame"></iframe> */
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
             <div class="block block-list">
                 <div class="block-title">
                     <strong><span>Block Title</span></strong>
                 </div>
                 <div class="block-content">
                     <p class="empty">Block Content</p>
                 </div>
             </div>
             <!--end_vde_element_2-->
             <!--end_vde_element_1-->
         </div>
     */
    var page = jQuery(window).vde_page();
    jQuery(page.vde_page('option', 'frameSelector')).triggerHandler('load');
    jQuery(page.vde_page('option', 'frameSelector')).contents().find("body:first").html(this.iframeContent);
    page.vde_page('destroy');
    var commentsExist = false;
    jQuery('*').contents().each(function () {
        if (this.nodeType == Node.COMMENT_NODE) {
            if (this.data.substr(0, 9) == 'start_vde') {
                commentsExist = true;
            } else if (this.data.substr(0, 7) == 'end_vde') {
                commentsExist = true;
            }
        }
    });
    assertEquals(false, commentsExist);
};
