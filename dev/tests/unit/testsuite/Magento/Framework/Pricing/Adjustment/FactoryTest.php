<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Pricing\Adjustment;

/**
 * Test class for \Magento\Framework\Pricing\Adjustment\Factory
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

    public function testCreate()
    {
        $adjustmentInterface = 'Magento\Framework\Pricing\Adjustment\AdjustmentInterface';
        $adjustmentFactory = $this->prepareAdjustmentFactory($adjustmentInterface);

        $this->assertInstanceOf(
            $adjustmentInterface,
            $adjustmentFactory->create($adjustmentInterface)
        );
    }

    /**
     * @param string $adjustmentInterface
     * @return object
     */
    protected function prepareAdjustmentFactory($adjustmentInterface)
    {
        return $this->objectManager->getObject(
            'Magento\Framework\Pricing\Adjustment\Factory',
            ['objectManager' => $this->prepareObjectManager($adjustmentInterface)]
        );
    }

    /**
     * @param string $adjustmentInterface
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\ObjectManager\ObjectManager
     */
    protected function prepareObjectManager($adjustmentInterface)
    {
        $objectManager = $this->getMock(
            'Magento\Framework\ObjectManager\ObjectManager',
            array('create'),
            array(),
            '',
            false
        );
        $objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->getMockForAbstractClass($adjustmentInterface)));
        return $objectManager;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateWithException()
    {
        $invalidAdjustmentInterface = 'Magento\Object';
        $adjustmentFactory = $this->prepareAdjustmentFactory($invalidAdjustmentInterface);
        $adjustmentFactory->create($invalidAdjustmentInterface);
    }
}
