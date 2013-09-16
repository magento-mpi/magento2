<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ImportExport_Model_Export_Entity_Eav_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test attribute code
     */
    const ATTRIBUTE_CODE = 'code1';
    /**#@-*/

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = array(
        Magento_Core_Model_AppInterface::ADMIN_STORE_ID => 'admin',
        1                                            => 'website1',
    );

    /**
     * Stores array (store id => code)
     *
     * @var array
     */
    protected $_stores = array(
        0 => 'admin',
        1 => 'store1',
    );

    /**
     * Attributes array
     *
     * @var array
     */
    protected $_attributes = array(
        array(
            'attribute_id'   => 1,
            'attribute_code' => self::ATTRIBUTE_CODE,
        )
    );

    /**
     * Customer data
     *
     * @var array
     */
    protected $_customerData = array(
        'website_id'         => 1,
        'store_id'           => 1,
        self::ATTRIBUTE_CODE => 1,
    );

    /**
     * Customer export model
     *
     * @var Magento_ImportExport_Model_Export_Entity_Eav_Customer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_ImportExport_Model_Export_Entity_Eav_Customer($this->_getModelDependencies());
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Create mocks for all $this->_model dependencies
     *
     * @return array
     */
    protected function _getModelDependencies()
    {
        $websiteManager = $this->getMock('stdClass', array('getWebsites'));
        $websiteManager->expects($this->once())
            ->method('getWebsites')
            ->will($this->returnCallback(array($this, 'getWebsites')));

        $storeManager = $this->getMock('stdClass', array('getStores'));
        $storeManager->expects($this->once())
            ->method('getStores')
            ->will($this->returnCallback(array($this, 'getStores')));

        $translator = $this->getMock('stdClass');

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $attributeCollection = new Magento_Data_Collection(
            $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false)
        );
        foreach ($this->_attributes as $attributeData) {
            $arguments = $objectManagerHelper->getConstructArguments('Magento_Eav_Model_Entity_Attribute_Abstract');
            $arguments['data'] = $attributeData;
            $attribute = $this->getMockForAbstractClass('Magento_Eav_Model_Entity_Attribute_Abstract',
                $arguments, '', true, true, true, array('_construct')
            );
            $attributeCollection->addItem($attribute);
        }

        $data = array(
            'website_manager'              => $websiteManager,
            'store_manager'                => $storeManager,
            'translator'                   => $translator,
            'attribute_collection'         => $attributeCollection,
            'page_size'                    => 1,
            'collection_by_pages_iterator' => 'not_used',
            'entity_type_id'               => 1,
            'customer_collection'          => 'not_used'
        );

        return $data;
    }

    /**
     * Get websites
     *
     * @param bool $withDefault
     * @return array
     */
    public function getWebsites($withDefault = false)
    {
        $websites = array();
        if (!$withDefault) {
            unset($websites[0]);
        }
        foreach ($this->_websites as $id => $code) {
            if (!$withDefault && $id == Magento_Core_Model_AppInterface::ADMIN_STORE_ID) {
                continue;
            }
            $websiteData = array(
                'id'   => $id,
                'code' => $code,
            );
            $websites[$id] = new Magento_Object($websiteData);
        }

        return $websites;
    }

    /**
     * Get stores
     *
     * @param bool $withDefault
     * @return array
     */
    public function getStores($withDefault = false)
    {
        $stores = array();
        if (!$withDefault) {
            unset($stores[0]);
        }
        foreach ($this->_stores as $id => $code) {
            if (!$withDefault && $id == 0) {
                continue;
            }
            $storeData = array(
                'id'   => $id,
                'code' => $code,
            );
            $stores[$id] = new Magento_Object($storeData);
        }

        return $stores;
    }

    /**
     * Test for method exportItem()
     *
     * @covers Magento_ImportExport_Model_Export_Entity_Eav_Customer::exportItem
     */
    public function testExportItem()
    {
        /** @var $writer Magento_ImportExport_Model_Export_Adapter_Abstract */
        $writer = $this->getMockForAbstractClass('Magento_ImportExport_Model_Export_Adapter_Abstract',
            array(), '', false, false, true, array('writeRow')
        );

        $writer->expects($this->once())
            ->method('writeRow')
            ->will($this->returnCallback(array($this, 'validateWriteRow')));

        $this->_model->setWriter($writer);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Magento_Core_Model_Abstract');
        $arguments['data'] = $this->_customerData;
        $item = $this->getMockForAbstractClass('Magento_Core_Model_Abstract', $arguments);

        $this->_model->exportItem($item);
    }

    /**
     * Validate data passed to writer's writeRow() method
     *
     * @param array $row
     */
    public function validateWriteRow(array $row)
    {
        $websiteColumn = Magento_ImportExport_Model_Export_Entity_Eav_Customer::COLUMN_WEBSITE;
        $storeColumn = Magento_ImportExport_Model_Export_Entity_Eav_Customer::COLUMN_STORE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$websiteColumn]);
        $this->assertEquals($this->_stores[$this->_customerData['store_id']], $row[$storeColumn]);
        $this->assertEquals($this->_customerData[self::ATTRIBUTE_CODE], $row[self::ATTRIBUTE_CODE]);
    }
}
