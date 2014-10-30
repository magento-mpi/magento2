<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * CustomerDetails Test
 */
class CustomerDetailsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * CustomerDetails
     *
     * @var CustomerDetails
     */
    private $_customerDetails;

    /**
     * Customer mock
     *
     * @var CustomerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerMock;

    /**
     * Address mock
     *
     * @var Address | \PHPUnit_Framework_MockObject_MockObject
     */
    private $_addressMock;

    public function setUp()
    {
        $this->_customerMock = $this->getMockBuilder(
            '\Magento\Customer\Api\Data\CustomerInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_addressMock = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\Address'
        )->disableOriginalConstructor()->getMock();
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder */
        $customerDetailsBuilder = $objectManager->getObject('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');
        $customerDetailsBuilder->setCustomer(
            $this->_customerMock
        )->setAddresses(
            array($this->_addressMock, $this->_addressMock)
        );
        $this->_customerDetails = new CustomerDetails($customerDetailsBuilder);
    }

    public function testGetCustomer()
    {
        $this->assertEquals($this->_customerMock, $this->_customerDetails->getCustomer());
    }

    public function testGetAddresses()
    {
        $this->assertEquals(array($this->_addressMock, $this->_addressMock), $this->_customerDetails->getAddresses());
    }
}
