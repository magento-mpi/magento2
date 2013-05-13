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
     * Tested class name
     *
     * @var string
     */
    protected $_testClassName = 'Mage_Customer_Model_Customer';

    /**
     * Test Mage_Customer_Model_Customer::sendPasswordResetConfirmationEmail()
     */
    public function testSendPasswordResetConfirmationEmail()
    {
        $storeId = rand(1, 10000);

        $objectMock = $this->getMockBuilder($this->_testClassName)
            ->disableOriginalConstructor()
            ->setMethods(array('getStoreId', '_getWebsiteStoreId', '_sendEmailTemplate'))
            ->getMock();
        $objectMock->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue(false));
        $objectMock->expects($this->once())
            ->method('_getWebsiteStoreId')
            ->will($this->returnValue($storeId));
        $objectMock->expects($this->once())
            ->method('_sendEmailTemplate')
            ->with(
                $this->equalTo(Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_TEMPLATE),
                $this->equalTo(Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_IDENTITY),
                $this->equalTo(array('customer' => $objectMock)),
                $this->equalTo($storeId));
        $result = $objectMock->sendPasswordResetConfirmationEmail();
        $this->assertInstanceOf($this->_testClassName, $result);
    }
}
