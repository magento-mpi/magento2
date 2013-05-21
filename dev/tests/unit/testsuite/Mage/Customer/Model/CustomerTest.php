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
 * Class Mage_Customer_Model_CustomerTest
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
        $storeId = rand(1, 10000);

        $this->_model->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue(false));
        $this->_model->expects($this->any())
            ->method('_getWebsiteStoreId')
            ->will($this->returnValue($storeId));
        $this->_model->expects($this->once())
            ->method('_sendEmailTemplate')
            ->with(
                $this->equalTo(Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_TEMPLATE),
                $this->equalTo(Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_IDENTITY),
                $this->equalTo(array('customer' => $this->_model)),
                $this->equalTo($storeId));
        $this->_model->sendPasswordResetConfirmationEmail();
    }
}
