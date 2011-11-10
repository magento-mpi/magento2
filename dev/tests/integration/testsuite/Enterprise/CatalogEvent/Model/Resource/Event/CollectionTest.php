<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_CatalogEvent
 * @magentoDataFixture Enterprise/CatalogEvent/_files/events.php
 */
class Enterprise_CatalogEvent_Model_Resource_Event_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_CatalogEvent_Model_Resource_Event_Collection
     */
    protected $_collection;

    protected function setUp()
    {
        if (Magento_Test_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('Bug MAGETWO-296');
        }
        $this->_collection = new Enterprise_CatalogEvent_Model_Resource_Event_Collection;
    }

    /**
     * Assert that collection contains expected item at expected index within the expected number of items
     *
     * @param int $expectedItemCount
     * @param int $expectedItemIndex
     * @param array $expectedItemData
     */
    protected function _assertCollectionData($expectedItemCount, $expectedItemIndex, array $expectedItemData)
    {
        $items = array_values($this->_collection->getItems());
        $this->assertEquals($expectedItemCount, count($items), 'Expected number of collection items.');

        /** @var $actualItem Enterprise_CatalogEvent_Model_Event */
        $actualItem = $items[$expectedItemIndex];

        $this->assertInstanceOf('Enterprise_CatalogEvent_Model_Event', $actualItem);
        foreach ($expectedItemData as $filedName => $expectedValue) {
            $actualValue = $actualItem->getDataUsingMethod($filedName);
            $this->assertEquals($expectedValue, $actualValue, "Field '{$filedName}' value doesn't match expectations.");
        }
    }

    public function loadDataProvider()
    {
        return array(
            'closed event' => array(
                'index' => 0,
                'data' => array(
                    'category_id'   => null,
                    'display_state' => 1/*Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE*/,
                    'sort_order'    => 30,
                    'status'        => 'closed'/*Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED*/,
                ),
            ),
            'open event' => array(
                'index' => 1,
                'data' => array(
                    'category_id'   => 1,
                    'display_state' => 2/*Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE*/,
                    'sort_order'    => 20,
                    'status'        => 'open'/*Enterprise_CatalogEvent_Model_Event::STATUS_OPEN*/,
                ),
            ),
            'upcoming event' => array(
                'index' => 2,
                'data' => array(
                    'category_id'   => 2,
                    'display_state' => 3,
                    /*Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE,
                        Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE*/
                    'sort_order'    => 10,
                    'status'        => 'upcoming'/*Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING*/,
                ),
            ),
        );
    }

    /**
     * @dataProvider loadDataProvider
     */
    public function testLoad($expectedItemIndex, array $expectedItemData)
    {
        $this->_collection
            ->addCategoryData()
            ->addImageData()
        ;
        $this->_assertCollectionData(3, $expectedItemIndex, $expectedItemData);
    }

    public function loadVisibleDataProvider()
    {
        $result = $this->loadDataProvider();

        unset($result['closed event']);
        $result['open event']['index']     = 0;
        $result['upcoming event']['index'] = 1;

        return $result;
    }

    /**
     * @dataProvider loadVisibleDataProvider
     */
    public function testLoadVisible($expectedItemIndex, array $expectedItemData)
    {
        $this->_collection
            ->addCategoryData()
            ->addImageData()
            ->addVisibilityFilter()
        ;
        $this->_assertCollectionData(2, $expectedItemIndex, $expectedItemData);
    }
}
