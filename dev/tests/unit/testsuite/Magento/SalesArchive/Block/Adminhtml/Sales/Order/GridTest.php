<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order;

/**
 * Class GridTest
 * @package Magento\SalesArchive\Block\Adminhtml\Sales\Order
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid
     */
    protected $grid;

    /**
     * @var \Magento\SalesArchive\Model\Resource\Order\Collection | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollection;

    /**
     * @var \Magento\Framework\UrlInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorization;

    /**
     * @var int
     */
    protected $size;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->size = 5;

        $this->urlBuilder = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $this->authorization = $this->getMockForAbstractClass('Magento\Framework\AuthorizationInterface');
        $this->authorization->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $context = $this->getMock('Magento\Backend\Block\Template\Context', [], [], '', false);
        $context->expects($this->once())->method('getUrlBuilder')->will($this->returnValue($this->urlBuilder));
        $context->expects($this->once())->method('getAuthorization')->will($this->returnValue($this->authorization));

        $this->orderCollection = $this->getMock(
            'Magento\SalesArchive\Model\Resource\Order\Collection',
            [],
            [],
            '',
            false
        );
        $this->orderCollection->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($this->size));

        $this->grid = $objectManager->getObject(
            'Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid',
            [
                'context' => $context,
                'orderCollection' => $this->orderCollection
            ]
        );
    }

    public function testCreate()
    {
        $reflectionProperty = new \ReflectionProperty('Magento\Backend\Block\Widget\Container', '_buttons');
        $reflectionProperty->setAccessible(true);
        $buttons = $reflectionProperty->getValue($this->grid);
        $this->assertArrayHasKey('go_to_archive', $buttons[0]);
    }
}
