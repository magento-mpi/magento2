<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Data_Form_Element_MultiselectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Data_Form_Element_Multiselect
     */
    protected $_model;

    protected function setUp()
    {
        $testHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $testHelper->getObject('Magento_Data_Form_Element_Editablemultiselect');
        $this->_model->setForm(new Magento_Object());
    }

    /**
     * Verify that hidden input is present in multiselect
     *
     * @covers Magento_Data_Form_Element_Multiselect::getElementHtml
     */
    public function testHiddenFieldPresentInMultiSelect()
    {
        $this->_model->setDisabled(true);
        $this->_model->setCanBeEmpty(true);
        $elementHtml = $this->_model->getElementHtml();
        $this->assertContains('<input type="hidden"', $elementHtml);
    }
}
