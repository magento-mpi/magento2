<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Plugin;

class TabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $collection \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection
     * @param $isOutputEnabled bool
     * @param $size int
     *
     * @dataProvider getGroupCollectionDataProvider
     */
    public function testAfterGetGroupCollection($collection, $isOutputEnabled, $size)
    {
        $moduleManager = $this->getMock('Magento\Framework\Module\Manager', [], [], '', false);
        $moduleManager->expects($this->once())
            ->method('isOutputEnabled')
            ->with('Magento_RecurringPayment')
            ->will($this->returnValue($isOutputEnabled)
         );

        $subject = $this->getMock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs', [], [], '', false);
        $object = new \Magento\RecurringPayment\Model\Plugin\Tabs($moduleManager);

        $collection = $object->afterGetGroupCollection($subject, $collection);
        $this->assertEquals($collection->getSize(), $size);
    }

    public function getGroupCollectionDataProvider()
    {
        $item1 = new \Magento\Framework\Object(['attribute_group_code' => 'recurring-payment']);
        $item2 = new \Magento\Framework\Object(['attribute_group_code' => 'data1']);
        $item3 = new \Magento\Framework\Object(['attribute_group_code' => 'data2']);

        $collection1 = new \Magento\Framework\Data\Collection(
            $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false)
        );
        $collection1->addItem($item1);
        $collection1->addItem($item2);

        $collection2 = clone $collection1;

        $collection3 = new \Magento\Framework\Data\Collection(
            $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false)
        );
        $collection3->addItem($item2);
        $collection3->addItem($item3);

        return [[$collection1, true, 2], [$collection2, false, 1], [$collection3, false, 2]];
    }
}
