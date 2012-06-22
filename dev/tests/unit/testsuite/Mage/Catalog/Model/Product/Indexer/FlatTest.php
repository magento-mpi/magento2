<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_Model_Product_Indexer_Flat
 */
class Mage_Catalog_Model_Product_Indexer_FlatTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Product_Indexer_Flat
     */
    protected $_model = null;

    /**
     * @var Mage_Index_Model_Event
     */
    protected $_event = null;

    public function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Product_Indexer_Flat;
        $this->_event = $this->getMock('Mage_Index_Model_Event', array('getFlatHelper', 'getEntity', 'getType', 'getDataObject'), array(), '', false);
    }

    public function testMatchEventAvailability()
    {
        $flatHelper = $this->getMock('Mage_Catalog_Helper_Product_Flat');
        $flatHelper->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $this->_event->expects($this->any())
            ->method('getFlatHelper')
            ->will($this->returnValue($flatHelper));

        $this->assertFalse($this->_model->matchEvent($this->_event));

        $flatHelper->expects($this->any())
            ->method('isBuilt')
            ->will($this->returnValue(false));

        $this->assertFalse($this->_model->matchEvent($this->_event));
    }

    /**
     * @dataProvider getEavAttributeProvider
     */
    public function testMatchEventForEavAttribute($attributeValue, $addFilterable, $origData, $data, $eventType,
        $result
    ) {
        $flatHelper = $this->getMock('Mage_Catalog_Helper_Product_Flat', array(), array(), '', false);
        $flatHelper->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $flatHelper->expects($this->any())
            ->method('isBuilt')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getFlatHelper')
            ->will($this->returnValue($flatHelper));

        $this->_event->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue(Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY));

        if ($attributeValue) {
            $attributeValue = $this->getMockBuilder('Mage_Catalog_Model_Resource_Eav_Attribute')
                ->disableOriginalConstructor()
                ->setMethods(array('getData', 'getOrigData'))
                ->getMock();
        }
        $this->_event->expects($this->any())
            ->method('getDataObject')
            ->will($this->returnValue($attributeValue));

        $flatHelper->expects($this->any())
            ->method('isAddFilterableAttributes')
            ->will($this->returnValue($addFilterable));

        if (!$attributeValue) {
            $this->assertEquals($result, $this->_model->matchEvent($this->_event));
            return;
        }

        $attributeValue->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($data));

        $attributeValue->expects($this->any())
            ->method('getOrigData')
            ->will($this->returnValueMap($origData));

        $this->_event->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($eventType));


        $this->assertEquals($result, $this->_model->matchEvent($this->_event));
    }

    public function testMatchEventForStoreForDelete()
    {
        $this->_prepareStoreConfiguration();

        $this->_event->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(Mage_Index_Model_Event::TYPE_DELETE));

        $this->assertTrue($this->_model->matchEvent($this->_event));
    }

    public function testMatchEventForEmptyStoreForSave()
    {
        $this->_prepareStoreConfiguration();

        $this->_event->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(Mage_Index_Model_Event::TYPE_SAVE));

        $this->_event->expects($this->any())
            ->method('getDataObject')
            ->will($this->returnValue(null));

        $this->assertFalse($this->_model->matchEvent($this->_event));
    }

    public function testMatchEventForOldStoreForSave()
    {
        $this->_prepareStoreConfiguration();

        $this->_event->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(Mage_Index_Model_Event::TYPE_SAVE));

        $store = $this->getMockBuilder('Mage_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();

        $store->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(false));

        $this->_event->expects($this->any())
            ->method('getDataObject')
            ->will($this->returnValue($store));

        $this->assertFalse($this->_model->matchEvent($this->_event));
    }

    public function testMatchEventForNewStoreForSave()
    {
        $this->_prepareStoreConfiguration();

        $this->_event->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(Mage_Index_Model_Event::TYPE_SAVE));

        $store = $this->getMockBuilder('Mage_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();

        $store->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getDataObject')
            ->will($this->returnValue($store));

        $this->assertTrue($this->_model->matchEvent($this->_event));
    }

    protected function _prepareStoreConfiguration()
    {
        $flatHelper = $this->getMock('Mage_Catalog_Helper_Product_Flat', array(), array(), '', false);
        $flatHelper->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $flatHelper->expects($this->any())
            ->method('isBuilt')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getFlatHelper')
            ->will($this->returnValue($flatHelper));

        $this->_event->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue(Mage_Core_Model_Store::ENTITY));
    }

    public function testMatchEventForEmptyStoreGroup()
    {
        $flatHelper = $this->getMock('Mage_Catalog_Helper_Product_Flat', array(), array(), '', false);
        $flatHelper->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $flatHelper->expects($this->any())
            ->method('isBuilt')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getFlatHelper')
            ->will($this->returnValue($flatHelper));

        $this->_event->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue(Mage_Core_Model_Store_Group::ENTITY));

        $this->_event->expects($this->any())
            ->method('getDataObject')
            ->will($this->returnValue(null));

        $this->assertFalse($this->_model->matchEvent($this->_event));
    }


    public function testMatchEventForNotChangedStoreGroup()
    {
        $flatHelper = $this->getMock('Mage_Catalog_Helper_Product_Flat', array(), array(), '', false);
        $flatHelper->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $flatHelper->expects($this->any())
            ->method('isBuilt')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getFlatHelper')
            ->will($this->returnValue($flatHelper));

        $this->_event->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue(Mage_Core_Model_Store_Group::ENTITY));

        $storeGroup = $this->getMockBuilder('Mage_Core_Model_Store_Group')
            ->disableOriginalConstructor()
            ->getMock();

        $storeGroup->expects($this->any())
            ->method('dataHasChangedFor')
            ->will($this->returnValue(false));

        $this->_event->expects($this->any())
            ->method('getDataObject')
            ->will($this->returnValue($storeGroup));

        $this->assertFalse($this->_model->matchEvent($this->_event));
    }

    public function testMatchEventForChangedStoreGroup()
    {
        $flatHelper = $this->getMock('Mage_Catalog_Helper_Product_Flat', array(), array(), '', false);
        $flatHelper->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $flatHelper->expects($this->any())
            ->method('isBuilt')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getFlatHelper')
            ->will($this->returnValue($flatHelper));

        $this->_event->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue(Mage_Core_Model_Store_Group::ENTITY));

        $storeGroup = $this->getMockBuilder('Mage_Core_Model_Store_Group')
            ->disableOriginalConstructor()
            ->getMock();

        $storeGroup->expects($this->any())
            ->method('dataHasChangedFor')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getDataObject')
            ->will($this->returnValue($storeGroup));

        $this->assertTrue($this->_model->matchEvent($this->_event));
    }

    public function testMatchEventParentFallback()
    {
        $flatHelper = $this->getMock('Mage_Catalog_Helper_Product_Flat', array(), array(), '', false);
        $flatHelper->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $flatHelper->expects($this->any())
            ->method('isBuilt')
            ->will($this->returnValue(true));

        $this->_event->expects($this->any())
            ->method('getFlatHelper')
            ->will($this->returnValue($flatHelper));

        $this->_event->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue('some_value'));

        $this->assertFalse($this->_model->matchEvent($this->_event));
    }

    public function testMatchEventCaching()
    {
        $this->testMatchEventForChangedStoreGroup();

        $storeGroup = $this->_event->getDataObject();

        $storeGroup->expects($this->any())
            ->method('dataHasChangedFor')
            ->will($this->returnValue(false));

        $this->assertTrue($this->_model->matchEvent($this->_event));

    }

    /**
     * Provider for testMatchEventForEavAttribute
     */
    public static function getEavAttributeProvider()
    {
        return array(
            //empty attribute case
            array(false, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, false),//Event Type, result
            //attribute exists, but shouldn't be matched
            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, false),//Event Type, result
            //Next cases describe situation that one valuable argument exists
            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 1),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 1),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, false),//Event Type, result

            array(true, true, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 1),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 1),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 1),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 1),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 1)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_DELETE, true),//Event Type, result

            //Mage_Index_Model_Event::TYPE_SAVE cases
            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, false),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, true, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, false),//Event Type, result

            array(true, true, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 1),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, true, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 1),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 1),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 1),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 1),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 1),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 1)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 0)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result

            array(true, false, //Attribute, isAddFilterable
                //Original attribute data
                array(
                    array('backend_type', 'not_static'),
                    array('is_filterable', 0),
                    array('used_in_product_listing', 0),
                    array('is_used_for_promo_rules', 0),
                    array('used_for_sort_by', 0)
                ),
                //Attribute data
                array(
                    array('backend_type', null, 'not_static'),
                    array('is_filterable', null, 0),
                    array('used_in_product_listing', null, 0),
                    array('is_used_for_promo_rules', null, 0),
                    array('used_for_sort_by', null, 1)
                ),
                Mage_Index_Model_Event::TYPE_SAVE, true),//Event Type, result
        );
    }

}
