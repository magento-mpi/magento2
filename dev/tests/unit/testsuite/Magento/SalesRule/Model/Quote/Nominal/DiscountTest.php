<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Quote\Nominal;

/**
 * Class DiscountTest
 */
class DiscountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Quote\Nominal\Discount
     */
    protected $discount;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->discount = $this->objectManager->getObject('Magento\SalesRule\Model\Quote\Nominal\Discount', []);
    }

    public function testFetch()
    {
        $addressMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInternalType('array', $this->discount->fetch($addressMock));
    }

    public function testGetAddressItems()
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $addressMock = $this->getMockBuilder('Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $addressMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();
        $validatorMock = $this->getMockBuilder('Magento\SalesRule\Model\Validator')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \Magento\SalesRule\Model\Quote\Discount $discount */
        $discount = $this->objectManager->getObject(
            'Magento\SalesRule\Model\Quote\Discount',
            ['storeManager' => $storeManagerMock, 'validator' => $validatorMock]
        );

        $this->assertInstanceOf(
            'Magento\Sales\Model\Quote\Address\Total\AbstractTotal',
            $discount->collect($addressMock)
        );
    }
}
