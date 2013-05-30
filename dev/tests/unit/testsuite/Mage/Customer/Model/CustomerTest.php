<?php
/**
 * Unit test for customer service layer Mage_Customer_Model_Customer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Customer_Model_Customer testing
 */
class Mage_Customer_Model_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock model instance
     */
    protected $_model;

    /**
     * Set required values
     */
    public function setUp()
    {
        $this->_model = $this->getMockBuilder('Mage_Customer_Model_Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getStoreId', '_sendEmailTemplate', '_getWebsiteStoreId'))
            ->getMock();
    }

    /**
     * Test Mage_Customer_Model_Customer::sendPasswordResetConfirmationEmail()
     */
    public function testSendPasswordResetConfirmationEmail()
    {
        $storeId = 1;

        $this->_model->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue(false));
        $this->_model->expects($this->any())
            ->method('_getWebsiteStoreId')
            ->will($this->returnValue($storeId));
        $this->_model->expects($this->once())
            ->method('_sendEmailTemplate')
            ->with(Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_TEMPLATE,
                Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_IDENTITY,
                array('customer' => $this->_model),
                $storeId);
        $this->assertEquals($this->_model, $this->_model->sendPasswordResetConfirmationEmail());
    }
}
