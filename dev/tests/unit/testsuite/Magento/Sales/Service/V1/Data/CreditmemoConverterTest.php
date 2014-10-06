<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class CreditmemoConverterTest
 */
class CreditmemoConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Data\CreditmemoConverter
     */
    protected $converter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loaderMock;

    public function setUp()
    {
        $this->loaderMock = $this->getMockBuilder('Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->converter = new \Magento\Sales\Service\V1\Data\CreditmemoConverter($this->loaderMock);
    }

    public function testGetModel()
    {
        $itemMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\CreditmemoItem')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $items = [$itemMock];

        $dataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Creditmemo')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $dataObjectMock->expects($this->once())
            ->method('getOrderId')
            ->willReturn(1);
        $dataObjectMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn(1);
        $dataObjectMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $creditmemoMock = $this->getMockBuilder('Magento\Sales\Model\Order\Creditmemo')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->loaderMock->expects($this->once())
            ->method('load')
            ->willReturn($creditmemoMock);
        $this->assertInstanceOf('Magento\Sales\Model\Order\Creditmemo', $this->converter->getModel($dataObjectMock));
    }
}
