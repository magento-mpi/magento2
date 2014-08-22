<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

/**
 * Test class for Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod
 */
class ShippingmethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod
     */
    protected $shippingmethod;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaShippinFactorygMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rmaShippinFactorygMock = $this->getMockBuilder('Magento\Rma\Model\ShippingFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->shippingmethod = $objectManager->getObject(
            'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod',
            [
                'shippingFactory' => $this->rmaShippinFactorygMock,
                'registry' => $this->registryMock
            ]
        );
    }

    /**
     * @param array $expected
     * @param array $actual
     * @dataProvider packageProvider
     */
    public function testGetPackages($expected, $actual)
    {
        $rmaShippingMock = $this->getMockBuilder('Magento\Rma\Model\Shipping')
            ->disableOriginalConstructor()
            ->setMethods(['getPackages', 'getShippingLabelByRma', '__wakeup'])
            ->getMock();
        $this->rmaShippinFactorygMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rmaShippingMock));

        $rmaMock = $this->getMockBuilder('Magento\Rma\Model\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $rmaShippingMock->expects($this->once())
            ->method('getShippingLabelByRma')
            ->with($rmaMock)
            ->will($this->returnSelf());
        $rmaShippingMock->expects($this->once())
            ->method('getPackages')
            ->will($this->returnValue($expected));

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('current_rma')
            ->will($this->returnValue($rmaMock));

        $this->assertEquals($actual, $this->shippingmethod->getPackages());
    }

    public function packageProvider()
    {
        return [
            [[], []],
            [['test'], ['test']]
        ];
    }
}
