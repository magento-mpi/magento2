<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Data
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Varien_Data_Form_Element_MultiselectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Data_Form_Element_Multiselect
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Varien_Data_Form_Element_Multiselect();
        $this->_model->setForm(new Varien_Object());
    }

    /**
     * Verify that hidden input is present in multiselect
     *
     * @covers Varien_Data_Form_Element_Multiselect::getElementHtml
     */
    public function testHiddenFieldPresentInMultiSelect()
    {
        $this->_model->setDisabled(true);
        $this->_model->setCanBeEmpty(true);
        $elementHtml = $this->_model->getElementHtml();
        $this->assertContains('<input type="hidden"', $elementHtml);
    }
}
