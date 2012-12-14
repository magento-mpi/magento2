/**
 * {license_notice}
 *
 * @category    mage.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
ButtonTest = TestCase('ButtonTest');
ButtonTest.prototype.testInit = function() {
    /*:DOC += <button id="test-button"></button><div id="event-target"></div>*/
    assertTrue(jQuery('#test-button').button().is(':ui-button'));
};
ButtonTest.prototype.testProcessDataAttr = function() {
    /*:DOC += <button id="test-button" data-widget-button="{&quot;event&quot;:&quot;testEvent&quot;,&quot;related&quot;:&quot;#event-target&quot;}"></button>
        <div id="event-target"></div>*/
    var button = jQuery('#test-button').button();
    assertEquals('testEvent', button.button('option', 'event'));
    assertEquals('#event-target', button.button('option', 'related'));
};
ButtonTest.prototype.testBind = function() {
    /*:DOC += <button id="test-button" data-widget-button="{&quot;event&quot;:&quot;testEvent&quot;,&quot;related&quot;:&quot;#event-target&quot;}"></button>
        <div id="event-target"></div>*/
    var testEventTriggered = false;
    jQuery('#event-target').on('testEvent', function(e) {
        testEventTriggered = true;
    });
    jQuery('#test-button').button().click();

    assertTrue(testEventTriggered);
};
