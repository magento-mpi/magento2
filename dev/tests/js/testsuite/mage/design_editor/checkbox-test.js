/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
CheckboxTest = TestCase('DesignEditor_CheckboxTest');
CheckboxTest.prototype.setUp = function() {
    /*:DOC += <div id="checkbox"></div> */
    jQuery(window).off();
};
CheckboxTest.prototype.tearDown = function() {
    var checkbox = jQuery('#checkbox');
    if (checkbox.length && checkbox.data('vde_checkbox')) {
        checkbox.vde_checkbox('destroy')
    }
};
CheckboxTest.prototype.testInit = function() {
    var checkbox = jQuery('#checkbox').vde_checkbox();
    assertEquals(true, checkbox.is(':vde-vde_checkbox'));
};
CheckboxTest.prototype.testDefaultOptions = function() {
    var checkbox = jQuery('#checkbox').vde_checkbox();
    assertEquals('checked', checkbox.vde_checkbox('option', 'checkedClass'));
};
CheckboxTest.prototype.testClickEvent = function() {
    var checkbox = jQuery('#checkbox').vde_checkbox();
    var checkedClass = checkbox.vde_checkbox('option', 'checkedClass');
    var checkedEventIsTriggered = false;
    var uncheckedEventIsTriggered = false;
    checkbox.on('checked.vde_checkbox', function() {checkedEventIsTriggered = true;});
    checkbox.on('unchecked.vde_checkbox', function() {uncheckedEventIsTriggered = true;});
    checkbox.trigger('click');
    assertEquals(true, checkbox.hasClass(checkedClass));
    assertEquals(true, checkedEventIsTriggered);
    assertEquals(false, uncheckedEventIsTriggered);
    checkbox.trigger('click');
    assertEquals(false, checkbox.hasClass(checkedClass));
    assertEquals(true, uncheckedEventIsTriggered);
};
