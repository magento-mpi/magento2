<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Sales\Model\Quote\Address;

/**
 * Test class for \Magento\Sales\Model\Order
 */
class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressFactoryMock;

    /**
     * @var \Magento\Sales\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    /**
     * @var \Magento\Sales\Model\Resource\Quote\Address\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressCollectionMock;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $quote;

    protected function setUp()
    {
        $this->quoteAddressFactoryMock = $this->getMock('Magento\Sales\Model\Quote\AddressFactory', array('create'),
            array(), '', false);
        $this->quoteAddressMock = $this->getMock('Magento\Sales\Model\Quote\Address', array(), array(), '', false);
        $this->quoteAddressCollectionMock = $this->getMock('Magento\Sales\Model\Resource\Quote\Address\Collection',
            array(), array(), '', false);

        $this->quoteAddressFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->any())->method('getCollection')
            ->will($this->returnValue($this->quoteAddressCollectionMock));

        $this->quote = (new ObjectManager($this))->getObject('Magento\Sales\Model\Quote', array(
            'quoteAddressFactory' => $this->quoteAddressFactoryMock,
        ));
    }

    /**
     * @param array $addresses
     * @param bool $expected
     * @dataProvider dataProviderForTestIsMultipleShippingAddresses
     */
    public function testIsMultipleShippingAddresses($addresses, $expected)
    {
        $this->quoteAddressCollectionMock->expects($this->any())->method('setQuoteFilter')
            ->will($this->returnValue($this->quoteAddressCollectionMock));
        $this->quoteAddressCollectionMock->expects($this->once())->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator($addresses)));

        $this->assertEquals($expected, $this->quote->isMultipleShippingAddresses());
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsMultipleShippingAddresses()
    {
        return array(
            array(
                array($this->getAddressMock(Address::TYPE_SHIPPING), $this->getAddressMock(Address::TYPE_SHIPPING)),
                true,
            ),
            array(
                array($this->getAddressMock(Address::TYPE_SHIPPING), $this->getAddressMock(Address::TYPE_BILLING)),
                false,
            ),
        );
    }

    /**
     * @param string $type One of \Magento\Customer\Model\Address\AbstractAddress::TYPE_ const
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAddressMock($type)
    {
        $shippingAddressMock = $this->getMock('Magento\Sales\Model\Quote\Address', array('getAddressType', '__wakeup'),
            array(), '', false);

        $shippingAddressMock->expects($this->any())->method('getAddressType')
            ->will($this->returnValue($type));
        $shippingAddressMock->expects($this->any())->method('isDeleted')
            ->will($this->returnValue(false));
        return $shippingAddressMock;
    }
}
