<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_GiftCard_Model_Observer
     */
    protected $_model;

    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    /**
     * Test that dependency injections passed to the constructor will not be duplicated in _data property
     */
    public function testConstructorValidArguments()
    {
        $this->_model = $this->_objectManager->getObject(
            'Magento_GiftCard_Model_Observer',
            [
                'itemsFactory' => $this->getMock(
                    'Magento_Sales_Model_Resource_Order_Invoice_Item_CollectionFactory', [], [], '', false
                ),
                'templateFactory' => $this->getMock(
                    'Magento_Core_Model_Email_TemplateFactory', [], [], '', false
                ),
                'invoiceFactory' => $this->getMock(
                    'Magento_Sales_Model_Order_InvoiceFactory', [], [], '', false
                ),
                'data' => [
                    'email_template_model' => $this->getMock('Magento_Core_Model_Email_Template', [], [], '', false),
                    'custom_field'         => 'custom_value',
                ]
            ]
        );
        $this->assertEquals(array('custom_field' => 'custom_value'), $this->_model->getData());
    }

    /**
     * Test that only valid model instance can be passed to the constructor
     *
     * @expectedException InvalidArgumentException
     */
    public function testConstructorInvalidArgument()
    {
        $this->_objectManager->getObject(
            'Magento_GiftCard_Model_Observer',
            [
                'itemsFactory' => $this->getMock(
                    'Magento_Sales_Model_Resource_Order_Invoice_Item_CollectionFactory', [], [], '', false
                ),
                'templateFactory' => $this->getMock(
                    'Magento_Core_Model_Email_TemplateFactory', [], [], '', false
                ),
                'invoiceFactory' => $this->getMock(
                    'Magento_Sales_Model_Order_InvoiceFactory', [], [], '', false
                ),
                'data' => ['email_template_model' => new stdClass()],
            ]
        );
    }
}
