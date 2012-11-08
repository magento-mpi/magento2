<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_Service_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Service_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_service;

    protected function setUp()
    {
        $helper = $this->getMockBuilder('Mage_Customer_Helper_Data')
            ->getMock();
        $this->_service = new Mage_Customer_Service_Customer(array(
            'helper' => $helper
        ));
    }

    protected function tearDown()
    {
        unset($this->_service);
    }

    /**
     * Test beforeSave and afterSave callback are set correctly
     */
    public function testSetBeforeSaveCallback()
    {
        $this->assertInstanceOf('Mage_Customer_Service_Customer', $this->_service->setBeforeSaveCallback('intval'));
        $this->assertAttributeEquals('intval', '_beforeSaveCallback', $this->_service);
    }

    /**
     * Test beforeSave and afterSave callback are set correctly
     */
    public function testSetAfterSaveCallback()
    {
        $this->assertInstanceOf('Mage_Customer_Service_Customer', $this->_service->setAfterSaveCallback('intval'));
        $this->assertAttributeEquals('intval', '_afterSaveCallback', $this->_service);
    }
}
