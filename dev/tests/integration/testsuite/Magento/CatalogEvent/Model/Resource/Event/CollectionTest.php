<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Model\Resource\Event;

/**
 * @magentoDataFixture Magento/CatalogEvent/_files/events.php
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogEvent\Model\Resource\Event\Collection'
        );
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

        /** @var $actualItem \Magento\CatalogEvent\Model\Event */
        $actualItem = $items[$expectedItemIndex];

        $this->assertInstanceOf('Magento\CatalogEvent\Model\Event', $actualItem);
        foreach ($expectedItemData as $filedName => $expectedValue) {
            $actualValue = $actualItem->getDataUsingMethod($filedName);
            $this->assertEquals(
                $expectedValue,
                $actualValue,
                "Field '{$filedName}' value doesn't match expectations."
            );
        }
    }

    public function loadDataProvider()
    {
        return array(
            'closed event' => array(
                'index' => 0,
                'data' => array(
                    'category_id' => null,
                    'display_state' => \Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE,
                    'sort_order' => 30,
                    'status' => \Magento\CatalogEvent\Model\Event::STATUS_CLOSED,
                    'image' => 'default_store_view.jpg'
                )
            ),
            'open event' => array(
                'index' => 1,
                'data' => array(
                    'category_id' => 1,
                    'display_state' => \Magento\CatalogEvent\Model\Event::DISPLAY_PRODUCT_PAGE,
                    'sort_order' => 20,
                    'status' => \Magento\CatalogEvent\Model\Event::STATUS_OPEN,
                    'image' => 'default_website.jpg'
                )
            ),
            'upcoming event' => array(
                'index' => 2,
                'data' => array(
                    'category_id' => 2,
                    'display_state' => 3,
                    /*\Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE,
                        \Magento\CatalogEvent\Model\Event::DISPLAY_PRODUCT_PAGE*/
                    'sort_order' => 10,
                    'status' => \Magento\CatalogEvent\Model\Event::STATUS_UPCOMING,
                    'image' => 'default_store_view.jpg'
                )
            )
        );
    }

    /**
     * @dataProvider loadDataProvider
     */
    public function testLoad($expectedItemIndex, array $expectedItemData)
    {
        $this->_collection->addCategoryData()->addImageData();
        $this->_assertCollectionData(3, $expectedItemIndex, $expectedItemData);
    }

    public function loadVisibleDataProvider()
    {
        $result = $this->loadDataProvider();

        unset($result['closed event']);
        $result['open event']['index'] = 0;
        $result['upcoming event']['index'] = 1;

        return $result;
    }

    /**
     * @dataProvider loadVisibleDataProvider
     */
    public function testLoadVisible($expectedItemIndex, array $expectedItemData)
    {
        $this->_collection->addCategoryData()->addImageData()->addVisibilityFilter();
        $this->_assertCollectionData(2, $expectedItemIndex, $expectedItemData);
    }

    /**
     * @dataProvider addFieldToFilterDataProvider
     */
    public function testAddFieldToFilter($value, $expectedCount, $expectedItemData)
    {
        $this->_collection->addCategoryData()->addImageData()->addFieldToFilter('display_state', $value);
        $this->_assertCollectionData($expectedCount, 0, $expectedItemData);
    }

    /**
     * Data for testAddFieldToFilter
     *
     * @return array
     */
    public function addFieldToFilterDataProvider()
    {
        $data = $this->loadDataProvider();

        return array(
            array('display_state' => \Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE,
                'expected_count' => 2,
                'data' => $data['closed event']['data']),
            array('display_state' => \Magento\CatalogEvent\Model\Event::DISPLAY_PRODUCT_PAGE,
                'expected_count' => 2,
                'data' => $data['open event']['data']),
            array('display_state' => 0,
                'expected_count' => 3,
                'data' => $data['closed event']['data'])
        );
    }
}
