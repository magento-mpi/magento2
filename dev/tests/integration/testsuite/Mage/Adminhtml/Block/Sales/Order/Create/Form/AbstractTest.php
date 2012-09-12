<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract.
 *
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Form_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    public function testAddAttributesToForm()
    {
        $block = $this->getMockForAbstractClass('Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract')
            ->setLayout(new Mage_Core_Model_Layout);

        $method = new ReflectionMethod(
            'Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract', '_addAttributesToForm');
        $method->setAccessible(true);

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('test_fieldset', array());
        $dateAttribute = new Mage_Customer_Model_Attribute(array(
            'attribute_code' => 'date',
            'backend_type' => 'datetime',
            'frontend_input' => 'date',
            'frontend_label' => 'Date',
        ));
        $attributes = array('date' => $dateAttribute);
        $method->invoke($block, $attributes, $fieldset);

        $element = $form->getElement('date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
