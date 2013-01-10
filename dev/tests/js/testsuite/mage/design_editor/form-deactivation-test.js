/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */

FormDeactivationTest = TestCase('DesignEditor_FormDeactivationTest');
FormDeactivationTest.prototype.testInit = function() {
    /*:DOC += <form id="test_design_editor_form" /><input type="submit" value="test button" /></form> */
    jQuery(document).vde_formDeactivation();
    jQuery('form').submit(function(e) {
        assertTrue(e.isDefaultPrevented());
    });
    jQuery('#test_design_editor_form').submit();
};
