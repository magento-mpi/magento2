<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Sidebar;


class AbstractSidebarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\AbstractSidebar
     */
    protected $abstractSidebar;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->itemMock = $this->getMock('Magento\Object', array('getQty'), array(), '', false);
        $this->abstractSidebar =
            $helper->getObject('Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\AbstractSidebar', array());
    }


    /**
     * @param int $itemQty
     * @param int|bool $qty
     * @param int $expectedValue
     * @dataProvider getItemQtyDataProvider
     */
    public function testGetItemQty($itemQty, $qty, $expectedValue)
    {
        $this->itemMock->expects($this->exactly($itemQty))->method('getQty')->will($this->returnValue($qty));
        $this->assertEquals($expectedValue, $this->abstractSidebar->getItemQty($this->itemMock));
    }

    public function getItemQtyDataProvider()
    {
        return array(
            'whenQtyIsset' => array(2, 10, 10),
            'whenQtyNotIsset' => array(1, false, 1)
        );
    }

    public function testIsConfigurationRequired()
    {
        $productTypeMock = $this->getMock('Magento\Catalog\Model\Product\Type', array(), array(), '', false);
        $this->assertEquals(false, $this->abstractSidebar->isConfigurationRequired($productTypeMock));
    }
}

