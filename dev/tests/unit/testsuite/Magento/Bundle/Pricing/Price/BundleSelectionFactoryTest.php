<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class BundleSelectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Bundle\Pricing\Price\BundleSelectionFactory */
    protected $bundleSelectionFactory;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;

    /** @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $bundleMock;

    /** @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $selectionMock;

    protected function setUp()
    {
        $this->bundleMock = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $this->selectionMock = $this->getMock('Magento\Pricing\Object\SaleableInterface');

        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->bundleSelectionFactory = $this->objectManagerHelper->getObject(
            'Magento\Bundle\Pricing\Price\BundleSelectionFactory',
            [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    public function testCreate()
    {
        $result = $this->getMock('Magento\Bundle\Pricing\Price\BundleSelectionPriceInterface');
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(BundleSelectionFactory::SELECTION_CLASS_DEFAULT),
                $this->equalTo(
                    [
                        'test' => 'some value',
                        'bundleProduct' => $this->bundleMock,
                        'salableItem' => $this->selectionMock,
                        'quantity' => 2.
                    ]
                )
            )
        ->will($this->returnValue($result));
        $this->assertSame(
            $result,
            $this->bundleSelectionFactory
                ->create($this->bundleMock, $this->selectionMock, 2., ['test' => 'some value'])
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateException()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(BundleSelectionFactory::SELECTION_CLASS_DEFAULT),
                $this->equalTo(
                    [
                        'test' => 'some value',
                        'bundleProduct' => $this->bundleMock,
                        'salableItem' => $this->selectionMock,
                        'quantity' => 2.
                    ]
                )
            )
            ->will($this->returnValue(new \stdClass()));
        $this->bundleSelectionFactory->create($this->bundleMock, $this->selectionMock, 2., ['test' => 'some value']);
    }

}
