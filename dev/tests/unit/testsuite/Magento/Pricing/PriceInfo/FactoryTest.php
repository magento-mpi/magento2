<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing\PriceInfo;

use Magento\Pricing\PriceInfo\Factory;

/**
 * Test class for \Magento\Pricing\PriceInfo\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var array
     */
    protected $types;

    /**
     * @var \Magento\Pricing\PriceInfo\Factory
     */
    protected $factory;

    /**
     * @var \Magento\Pricing\Price\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pricesMock;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * SetUp test
     */
    public function setUp()
    {
        $this->objectManagerMock = $this->getMock(
            'Magento\ObjectManager\ObjectManager',
            [],
            [],
            '',
            false
        );
        $this->pricesMock = $this->getMock(
            'Magento\Pricing\Price\Collection',
            [],
            [],
            '',
            false
        );
        $this->saleableItemMock = $this->getMockForAbstractClass(
            'Magento\Pricing\Object\SaleableInterface',
            [],
            '',
            false,
            true,
            true,
            ['getQty']
        );
        $this->priceInfoMock = $this->getMockForAbstractClass(
            'Magento\Pricing\PriceInfoInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->types = [
            'default' => [
                'infoClass' => 'Price\PriceInfo\Default',
                'prices' => 'Price\Collection\Default'
            ],
            'configurable' => [
                'infoClass' => 'Price\PriceInfo\Configurable',
                'prices' => 'Price\Collection\Configurable'
            ],
        ];
        $this->factory = new Factory($this->types, $this->objectManagerMock);
    }

    public function createPriceInfoDataProvider()
    {
        return [
            [
                'simple',
                1,
                'Price\PriceInfo\Default',
                'Price\Collection\Default'
            ],
            [
                'configurable',
                2,
                'Price\PriceInfo\Configurable',
                'Price\Collection\Configurable'
            ]
        ];
    }

    /**
     * @param $typeId
     * @param $quantity
     * @param $infoClass
     * @param $prices
     * @dataProvider createPriceInfoDataProvider
     */
    public function testCreate($typeId, $quantity, $infoClass, $prices)
    {
        $this->saleableItemMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($typeId));
        $this->saleableItemMock->expects($this->once())
            ->method('getQty')
            ->will($this->returnValue($quantity));

        $this->objectManagerMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValueMap(
                [
                    [
                        $prices,
                        [
                            'saleableItem' => $this->saleableItemMock,
                            'quantity' => $quantity
                        ],
                        $this->pricesMock
                    ],
                    [
                        $infoClass,
                        [
                            'saleableItem' => $this->saleableItemMock,
                            'quantity' => $quantity,
                            'prices' => $this->pricesMock
                        ],
                        $this->priceInfoMock
                    ]
                ]
            ));
        $this->assertEquals($this->priceInfoMock, $this->factory->create($this->saleableItemMock, []));
    }
}
