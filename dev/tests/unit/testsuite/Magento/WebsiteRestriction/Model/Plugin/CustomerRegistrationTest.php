<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_WebsiteRestriction_Model_Plugin_CustomerRegistrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_WebsiteRestriction_Model_Plugin_CustomerRegistration
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_restrictionHelper;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $this->_restrictionHelper = $this->getMock('Magento_WebsiteRestriction_Helper_Data',
            array(), array(), '', false);
        $this->_model = new Magento_WebsiteRestriction_Model_Plugin_CustomerRegistration(
            $this->_storeManagerMock,
            $this->_restrictionHelper
        );
    }

    public function testAfterIsRegistrationIsAllowedRestrictsRegistrationIfRestrictionModeForbidsIt()
    {
        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $storeMock->expects($this->any())
            ->method('isAdmin')
            ->will($this->returnValue(false));
        $storeMock->expects($this->any())
            ->method('getConfig')
            ->with(Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE)
            ->will($this->returnValue(Magento_WebsiteRestriction_Model_Mode::ALLOW_NONE));
        $this->_storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(null)
            ->will($this->returnValue($storeMock));
        $this->_restrictionHelper->expects($this->any())
            ->method('getIsRestrictionEnabled')
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->afterIsRegistrationAllowed(true));
    }
}
