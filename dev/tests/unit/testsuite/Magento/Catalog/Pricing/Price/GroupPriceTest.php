<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

/**
 * Group price test
 */
class GroupPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @param array|null $groupPrice
     * @param int $customerGroup
     * @param float $expected
     *
     * @dataProvider groupPriceDataProvider
     */
    public function testGroupPrice($groupPrice, $customerGroup, $expected)
    {
        $salableItemMock = $this->prepareSalableItem($groupPrice);
        $sessionMock = $this->prepareSession($salableItemMock, $customerGroup);
        $groupPriceMock = $this->objectManager->getObject(
            'Magento\Catalog\Pricing\Price\GroupPrice',
            [
                'salableItem'     => $salableItemMock,
                'customerSession' => $sessionMock
            ]
        );
        $this->assertEquals($expected, $groupPriceMock->getValue());
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product $salableItemMock
     * @param int $customerGroup
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Session
     */
    protected function prepareSession($salableItemMock, $customerGroup)
    {
        $session = $this->getMock('Magento\Customer\Model\Session', ['getCustomerGroupId'], [], '', false);
        $session->expects($this->any())
            ->method('getCustomerGroupId')
            ->will($this->returnValue($customerGroup));

        $salableItemMock->expects($this->any())
            ->method('getCustomerGroupId')
            ->will($this->returnValue(false));

        return $session;
    }

    /**
     * @dataProvider groupPriceNonExistDataProvider
     *
     * @param array|null $groupPrice
     * @param float $expected
     */
    public function testGroupPriceNonExist($groupPrice, $expected)
    {
        $groupPriceMock = $this->objectManager->getObject(
            'Magento\Catalog\Pricing\Price\GroupPrice',
            [
                'salableItem'     => $this->prepareSalableItem($groupPrice),
                'customerSession' => $this->getMock('Magento\Customer\Model\Session', [], [], '', false)
            ]
        );

        $this->assertEquals($expected, $groupPriceMock->getValue());
    }

    /**
     * @param array|null $groupPrice
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareSalableItem($groupPrice)
    {
        $salableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getCustomerGroupId', 'getData', 'getPrice', 'getPriceInfo', 'getResource', '__wakeup'],
            [],
            '',
            false
        );

        $salableItemMock->expects($this->at(1))
            ->method('getData')
            ->will($this->returnValue(null));

        $salableItemMock->expects($this->at(2))
            ->method('getData')
            ->will($this->returnValue($groupPrice));

        $salableItemMock->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($this->prepareSalableItemResource()));

        $priceInfo = $this->getMockBuilder(
            'Magento\Pricing\PriceInfoInterface'
        )->disableOriginalConstructor()->getMockForAbstractClass();

        $priceInfo->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue([]));

        $salableItemMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));

        return $salableItemMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Resource\Product
     */
    protected function prepareSalableItemResource()
    {
        $resourceMock = $this->getMockBuilder(
            'Magento\Catalog\Model\Resource\Product'
        )->disableOriginalConstructor()->setMethods(['getAttribute', '__wakeup'])->getMock();

        $attributeMock = $this->getMock(
            'Magento\Object',
            ['getBackend', 'afterLoad'],
            [],
            '',
            false
        );

        $attributeMock->expects($this->any())
            ->method('getBackend')
            ->will($this->returnValue($attributeMock));

        $attributeMock->expects($this->any())
            ->method('afterLoad')
            ->will($this->returnValue($attributeMock));

        $resourceMock->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($attributeMock));

        return $resourceMock;
    }

    /**
     * @return array
     */
    public function groupPriceDataProvider()
    {
        return [
            [
                'groupPrice' => [
                    [
                        'cust_group'    => 1,
                        'website_price' => 90.9
                    ],
                    [
                        'cust_group'    => 2,
                        'website_price' => 80.8
                    ],
                    [
                        'cust_group'    => 1,
                        'website_price' => 70.7
                    ]
                ],
                'customer_group'   => 1,
                'expected'         => 90.9
            ],
            [
                'groupPrice' => [
                    [
                        'cust_group'    => 2,
                        'website_price' => 10.1
                    ],
                    [
                        'cust_group'    => 1,
                        'website_price' => 20.2
                    ],
                ],
                'customer_group'   => 1,
                'expected'         => 20.2
            ],
            [
                'groupPrice' => [
                    [
                        'cust_group'    => 1,
                        'website_price' => 90.9
                    ],
                ],
                'customer_group'   => 2,
                'expected'         => false
            ]
        ];
    }

    /**
     * @return array
     */
    public function groupPriceNonExistDataProvider()
    {
        return [
            [
                'groupPrice'       => null,
                'expected'         => false
            ]
        ];
    }
}
