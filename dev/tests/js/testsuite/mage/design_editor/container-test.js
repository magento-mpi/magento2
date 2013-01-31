/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
ContainerTest = TestCase('DesignEditor_ContainerTest');
ContainerTest.prototype.testInit = function() {
    /*:DOC += <div class="vde_element_wrapper vde_container" id="container"></div> */
    var container = jQuery('#container').vde_container();
    assertEquals(true, container.is(':vde-vde_container'));
    container.vde_container('destroy');
};
ContainerTest.prototype.testDefaultOptions = function() {
    /*:DOC += <div class="vde_element_wrapper vde_container" id="container"></div> */
    var container = jQuery('#container').vde_container();
    assertEquals('pointer', container.vde_container('option', 'tolerance'));
    assertEquals(true, container.vde_container('option', 'revert'));
    assertEquals('.vde_element_wrapper.vde_container', container.vde_container('option', 'connectWithSelector'));
    assertEquals('vde_placeholder', container.vde_container('option', 'placeholder'));
    assertEquals('vde_container_hover', container.vde_container('option', 'hoverClass'));
    assertEquals('.vde_element_wrapper.vde_draggable', container.vde_container('option', 'items'));
    assertEquals('clone', container.vde_container('option', 'helper'));
    assertEquals('body', container.vde_container('option', 'appendTo'));
    container.vde_container('destroy');
};
ContainerTest.prototype.testStartCallback = function() {
    /*:DOC +=
    <div>
         <div class="vde_element_wrapper vde_container" id="container" />
         <div class="vde_element_wrapper vde_container" />
    </div>
    */
    var containers = jQuery(".vde_element_wrapper.vde_container").vde_container();
    var container = jQuery("#container");
    var uiMock = {
        placeholder: jQuery('<div style="height:0px;"></div>'),
        helper: jQuery('<div style="height:100px;"></div>'),
        item: container
    };
    var startCallback = container.vde_container('option', 'start');
    assertNotEquals(uiMock.helper.outerHeight(true) + 'px', container.css('min-height'));
    startCallback('start', uiMock);
    assertEquals(uiMock.helper.outerHeight(true) + 'px', container.css('min-height'));
    var connectedWithOtherContainers = container.vde_container('option', 'connectWith').size() > 0;
    assertTrue(connectedWithOtherContainers);
    containers.vde_container('destroy');
};
ContainerTest.prototype.testOverCallback = function() {
    /*:DOC += <div class="vde_element_wrapper vde_container" id="container" /> */
    var container = jQuery("#container").vde_container();
    var hoverClass = container.vde_container('option', 'hoverClass');
    var overCallback = container.vde_container('option', 'over');
    var helperHeight = 100;
    var uiMock = {
        helper: jQuery('<div />', {height: helperHeight})
    };
    overCallback('over', uiMock);
    assertTrue(container.hasClass(hoverClass));
    assertEquals(container.css('min-height'), uiMock.helper.outerHeight(true) + 'px');
    container.vde_container('destroy');
};
// "out" callback is deprecated and was replaced by "stop" callback
ContainerTest.prototype.testStopCallback = function() {
    /*:DOC += <div class="vde_element_wrapper vde_container" id="container" /> */
    var container = jQuery("#container").vde_container();
    var hoverClass = container.vde_container('option', 'hoverClass');
    var stopCallback = container.vde_container('option', 'stop');
    stopCallback('stop', {});
    assertFalse(container.hasClass(hoverClass));
    container.vde_container('destroy');
};
