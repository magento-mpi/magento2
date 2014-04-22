<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Pricing\PriceInfo;

/**
 * Test class for \Magento\Framework\Pricing\PriceInfo\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
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
     * @dataProvider priceInfoClassesProvider
     */
    public function testCreate($types, $type, $expected)
    {
        $priceInfoFactory = $this->preparePriceInfoFactory(
            $expected,
            $types
        );

        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getTypeId', 'getQty', '__wakeup'],
            [],
            '',
            false
        );

        $productMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue($type));

        $productMock->expects($this->any())
            ->method('getQty')
            ->will($this->returnValue(1));

        $this->assertInstanceOf(
            $expected,
            $priceInfoFactory->create($productMock)
        );
    }

    /**
     * @param string $priceInfoInterface
     * @param array $types
     * @return object
     */
    protected function preparePriceInfoFactory($priceInfoInterface, $types = [])
    {
        return $this->objectManager->getObject(
            'Magento\Framework\Pricing\PriceInfo\Factory',
            [
                'types' => $types,
                'objectManager' => $this->prepareObjectManager($priceInfoInterface)
            ]
        );
    }

    /**
     * @param string $priceInfoInterface
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\ObjectManager\ObjectManager
     */
    protected function prepareObjectManager($priceInfoInterface)
    {
        $objectManager = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', ['create'], [], '', false);
        $objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->getMockForAbstractClass($priceInfoInterface)));
        return $objectManager;
    }

    /**
     * @return array
     */
    public function priceInfoClassesProvider()
    {
        return [
            [
                ['new_type' => 'Magento\Framework\Pricing\PriceInfo\Base'],
                'new_type',
                'Magento\Framework\Pricing\PriceInfoInterface'
            ],
            [
                [],
                'unknown',
                'Magento\Framework\Pricing\PriceInfoInterface'
            ]
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateWithException()
    {
        $invalidPriceInfoInterface = 'Magento\Object';
        $priceInfoFactory = $this->preparePriceInfoFactory($invalidPriceInfoInterface);
        $priceInfoFactory->create(
            $this->getMock('Magento\Catalog\Model\Product', ['__wakeup'], [], '', false)
        );
    }
}
