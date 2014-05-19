<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Model\Category;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class EventListTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogEvent\Model\Category\EventList */
    protected $eventList;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerInterface;

    /** @var \Magento\CatalogEvent\Model\Resource\Event\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactory;

    /** @var \Magento\CatalogEvent\Model\Resource\Event\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $eventCollection;

    /** @var \Magento\CatalogEvent\Model\Resource\EventFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $eventFactory;

    /** @var \Magento\CatalogEvent\Model\Resource\Event|\PHPUnit_Framework_MockObject_MockObject */
    protected $resourceEvent;

    protected function setUp()
    {
        $this->registry = $this->getMock('Magento\Framework\Registry');
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->collectionFactory = $this->getMock(
            'Magento\CatalogEvent\Model\Resource\Event\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->eventFactory = $this->getMock(
            'Magento\CatalogEvent\Model\Resource\EventFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->eventCollection = $this->getMock(
            'Magento\CatalogEvent\Model\Resource\Event\Collection',
            [],
            [],
            '',
            false
        );
        $this->collectionFactory->expects($this->any())->method('create')->will(
            $this->returnValue($this->eventCollection)
        );
        $this->resourceEvent = $this->getMock('Magento\CatalogEvent\Model\Resource\Event', [], [], '', false);
        $this->eventFactory->expects($this->any())->method('create')->will(
            $this->returnValue($this->resourceEvent)
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->eventList = $this->objectManagerHelper->getObject(
            'Magento\CatalogEvent\Model\Category\EventList',
            [
                'coreRegistry' => $this->registry,
                'storeManager' => $this->storeManagerInterface,
                'eventCollectionFactory' => $this->collectionFactory,
                'eventFactory' => $this->eventFactory
            ]
        );
    }

    public function testGetEventInStoreFromCurrentCategory()
    {
        $categoryId = 1;
        /** @var \Magento\CatalogEvent\Model\Event $event */
        $event = $this->getMock('Magento\CatalogEvent\Model\Event', [], [], '', false);
        /** @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject $category */
        $category = $this->objectManagerHelper->getObject(
            '\Magento\Catalog\Model\Category',
            [
                'data' => ['id' => $categoryId, 'event' => $event]
            ]
        );
        $this->registry->expects($this->any())->method('registry')->with('current_category')->will(
            $this->returnValue($category)
        );
        $returnEvent = $this->eventList->getEventInStore($categoryId);
        $this->assertEquals($event, $returnEvent);
    }

    /**
     * Data provider for getting list of categories from store
     *
     * @return array
     */
    public function getEventInStoreDataProvider()
    {
        return array(
            array(
                array(2 => 3, 3 => null, 4 => null),
                2,
                3
            ),
            array(
                array(2 => 3, 3 => null, 4 => null),
                4,
                null
            ),
            array(
                array(2 => 3, 3 => null, 4 => null),
                5,
                false
            )
        );
    }

    /**
     * @param array $categoryList
     * @param int $categoryId
     * @param mixed $expectedResult
     *
     * @dataProvider getEventInStoreDataProvider
     */
    public function testGetEventInStore($categoryList, $categoryId, $expectedResult)
    {
        /** @var $store \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject */
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerInterface->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $this->resourceEvent->expects($this->once())->method('getCategoryIdsWithEvent')->will(
            $this->returnValue($categoryList)
        );
        $this->storeManagerInterface->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $eventCollectionReturnMap = array();
        foreach ($categoryList as $eventId) {
            if ($eventId) {
                $eventCollectionReturnMap[] = [$eventId, $eventId];
            }
        }
        $this->eventCollection->expects($this->any())->method('getItemById')->will(
            $this->returnValueMap($eventCollectionReturnMap)
        );
        $returnEvent = $this->eventList->getEventInStore($categoryId);
        $this->assertEquals($expectedResult, $returnEvent);
    }

    /**
     * Data provider for category-event association array
     *
     * @return array
     */
    public function getCategoryListDataProvider()
    {
        return array(
            array(
                array(2 => 3, 3 => null, 4 => null),
                1
            ),
            array(
                array(4=>3, 3=>1, 5=>4),
                3
            ),
            array(
                array(),
                0
            ),
            array(
                array(2 => null, 3 => null, 4 => null, 10 => null),
                0
            ),
        );
    }

    /**
     * @param array $categoryList
     * @param int $getItemCallNumber
     *
     * @dataProvider getCategoryListDataProvider
     */
    public function testGetEventToCategoriesList($categoryList, $getItemCallNumber)
    {
        /** @var $store \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject */
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerInterface->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $this->resourceEvent->expects($this->once())->method('getCategoryIdsWithEvent')->will(
            $this->returnValue($categoryList)
        );

        $event = new \Magento\Framework\Object();
        $this->eventCollection->expects($this->exactly($getItemCallNumber))->method('getItemById')->will(
            $this->returnValue($event)
        );
        $eventsToCategory = $this->eventList->getEventToCategoriesList();
        $this->assertInternalType('array', $eventsToCategory);
        foreach($categoryList as $key => $value) {
            if(!is_null($value)) {
                $this->assertInstanceOf('\Magento\Framework\Object', $eventsToCategory[$key]);
            } else {
                $this->assertNull($eventsToCategory[$key]);
            }
        }
    }

    public function testGetEventCollectionWithIds()
    {
        $this->eventCollection->expects($this->once())->method('addFieldToFilter');
        $collection = $this->eventList->getEventCollection(array(1, 3));
        $this->assertInstanceOf('\Magento\CatalogEvent\Model\Resource\Event\Collection', $collection);
    }

    public function testGetEventCollectionWithoutIds()
    {
        $this->eventCollection->expects($this->never())->method('addFieldToFilter');
        $collection = $this->eventList->getEventCollection();
        $this->assertInstanceOf('\Magento\CatalogEvent\Model\Resource\Event\Collection', $collection);
    }
}
