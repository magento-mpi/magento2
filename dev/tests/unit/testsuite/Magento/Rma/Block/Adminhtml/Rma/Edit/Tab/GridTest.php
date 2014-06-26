<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items;

/**
 * Class GridTest
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid
     */
    protected $grid;

    /**
     * @var \Magento\Rma\Model\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->itemMock = $this->getMock(
            'Magento\Rma\Model\Item',
            ['getReturnableQty', '__wakeup'],
            [],
            '',
            false
        );
        $this->grid = $objectManager->getObject('Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid');
    }

    /**
     *  test method getRemainingQty
     */
    public function testGetRemainingQty()
    {
        $this->itemMock->expects($this->once())
            ->method('getReturnableQty')
            ->will($this->returnValue(100.50));
        $this->assertEquals(100.50, $this->grid->getRemainingQty($this->itemMock));
    }
}
