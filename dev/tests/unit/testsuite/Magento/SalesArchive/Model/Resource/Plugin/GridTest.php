<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource\Plugin;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class GridTest
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\GridPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gridPoolSource;
    /**
     * @var \Magento\SalesArchive\Model\Resource\Archive|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $archiveSource;
    /**
     * @var \Magento\SalesArchive\Model\Resource\Plugin\Grid
     */
    protected $plugin;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->gridPoolSource = $this->getMock('Magento\Sales\Model\Resource\GridPool', [], [], '', false);
        $this->archiveSource = $this->getMock('Magento\SalesArchive\Model\Resource\Archive', [], [], '', false);

        $this->plugin = $objectManager->getObject(
            'Magento\SalesArchive\Model\Resource\Plugin\Grid',
            [
                'gridPool' => $this->gridPoolSource,
                'archive' => $this->archiveSource
            ]
        );
    }

    public function testAroundRefreshOrderInArchive()
    {
        $grid = $this->getMock('Magento\Sales\Model\Resource\Order\Grid', [], [], '', false);
        $value = '15';
        $field = null;
        $callable = function ($value, $field) {
            return false;
        };

        $this->archiveSource->expects($this->once())
            ->method('isOrderInArchive')
            ->will(
                $this->returnValue(true)
            );

        $this->archiveSource->expects($this->once())
            ->method('removeOrdersFromArchiveById');

        $this->gridPoolSource->expects($this->once())
            ->method('refreshByOrderId')
            ->willReturn(true);

        $this->assertTrue($this->plugin->aroundRefresh($grid, $callable, $value, $field));
    }

    public function testAroundRefreshOrderNotInArchive()
    {
        $grid = $this->getMock('Magento\Sales\Model\Resource\Order\Grid', [], [], '', false);
        $callable = function ($value, $field) {
            return false;
        };
        $value = '15';
        $field = null;

        $this->archiveSource->expects($this->once())
            ->method('isOrderInArchive')
            ->will(
                $this->returnValue(false)
            );

        $this->archiveSource->expects($this->never())
            ->method('removeOrdersFromArchiveById');

        $this->gridPoolSource->expects($this->never())
            ->method('refreshByOrderId');

        $this->assertFalse($this->plugin->aroundRefresh($grid, $callable, $value, $field));
    }
}
