<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

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
     * @var Customer | \PHPUnit_Framework_MockObject_MockObject
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
        $this->_customerMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_addressMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $data = [
            'customer' => $this->_customerMock,
            'addresses' => [$this->_addressMock, $this->_addressMock],
        ];
        $this->_customerDetails = new CustomerDetails($data);
    }

    public function testGetCustomer()
    {
        $this->assertEquals($this->_customerMock, $this->_customerDetails->getCustomer());
    }

    public function testGetAddresses()
    {
        $this->assertEquals(
            [$this->_addressMock, $this->_addressMock],
            $this->_customerDetails->getAddresses()
        );
    }
}
