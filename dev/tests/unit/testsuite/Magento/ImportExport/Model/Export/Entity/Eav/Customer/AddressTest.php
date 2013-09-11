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

class Magento_ImportExport_Model_Export_Entity_Eav_Customer_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test attribute code
     */
    const ATTRIBUTE_CODE = 'code1';

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
        'id'                 => 1,
        'website_id'         => 1,
        'store_id'           => 1,
        'email'              => '@email@domain.com',
        self::ATTRIBUTE_CODE => 1,
        'default_billing'    => 1,
        'default_shipping'   => 1,
    );

    /**
     * Customer address data
     *
     * @var array
     */
    protected $_addressData = array(
        'id'                 => 1,
        'entity_id'          => 1,
        'parent_id'          => 1,
        self::ATTRIBUTE_CODE => 1,
    );

    /**
     * ObjectManager helper
     *
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectManager;

    /**
     * Customer address export model
     *
     * @var Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address
     */
    protected $_model;

    public function setUp()
    {
        $this->_objectManager = new Magento_TestFramework_Helper_ObjectManager($this);

        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $this->_model = new Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address(
            $coreStoreConfig,
            $this->_getModelDependencies()
        );
    }

    public function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManager);
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

        $translator = $this->getMock('stdClass');

        /** @var $attributeCollection Magento_Data_Collection|PHPUnit_Framework_TestCase */
        $attributeCollection = $this->getMock('Magento_Data_Collection', array('getEntityTypeCode'));
        $attributeCollection->expects($this->once())
            ->method('getEntityTypeCode')
            ->will($this->returnValue('customer_address'));
        foreach ($this->_attributes as $attributeData) {
            $arguments = $this->_objectManager->getConstructArguments('Magento_Eav_Model_Entity_Attribute_Abstract');
            $arguments['data'] = $attributeData;
            $attribute = $this->getMockForAbstractClass('Magento_Eav_Model_Entity_Attribute_Abstract',
                $arguments, '', true, true, true, array('_construct')
            );
            $attributeCollection->addItem($attribute);
        }

        $byPagesIterator = $this->getMock('stdClass', array('iterate'));
        $byPagesIterator->expects($this->once())
            ->method('iterate')
            ->will($this->returnCallback(array($this, 'iterate')));

        $customerCollection = $this->getMock(
            'Magento_Data_Collection_Db', array('addAttributeToSelect'), array(), '', false
        );

        $customerEntity = $this->getMock('stdClass', array('filterEntityCollection', 'setParameters'));
        $customerEntity->expects($this->any())
            ->method('filterEntityCollection')
            ->will($this->returnArgument(0));
        $customerEntity->expects($this->any())
            ->method('setParameters')
            ->will($this->returnSelf());

        $data = array(
            'website_manager'              => $websiteManager,
            'store_manager'                => 'not_used',
            'translator'                   => $translator,
            'attribute_collection'         => $attributeCollection,
            'page_size'                    => 1,
            'collection_by_pages_iterator' => $byPagesIterator,
            'entity_type_id'               => 1,
            'customer_collection'          => $customerCollection,
            'customer_entity'              => $customerEntity,
            'address_collection'           => 'not_used',
        );

        return $data;
    }

    /**
     * Get websites stub
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
     * Iterate stub
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Magento_Data_Collection_Db $collection
     * @param int $pageSize
     * @param array $callbacks
     */
    public function iterate(Magento_Data_Collection_Db $collection, $pageSize, array $callbacks)
    {
        $arguments = $this->_objectManager->getConstructArguments('Magento_Customer_Model_Customer');
        $arguments['data'] = $this->_customerData;
        /** @var $customer Magento_Customer_Model_Customer */
        $customer = $this->getMock('Magento_Customer_Model_Customer', array('_construct'), $arguments);

        foreach ($callbacks as $callback) {
            call_user_func($callback, $customer);
        }
    }

    /**
     * Test for method exportItem()
     *
     * @covers Magento_ImportExport_Model_Export_Entity_Eav_Customer::exportItem
     */
    public function testExportItem()
    {
        $writer = $this->getMockForAbstractClass('Magento_ImportExport_Model_Export_Adapter_Abstract',
            array(), '', false, false, true, array('writeRow')
        );

        $writer->expects($this->once())
            ->method('writeRow')
            ->will($this->returnCallback(array($this, 'validateWriteRow')));

        $this->_model->setWriter($writer);
        $this->_model->setParameters(array());

        $arguments = $this->_objectManager->getConstructArguments('Magento_Core_Model_Abstract');
        $arguments['data'] = $this->_addressData;
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
        $billingColumn = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_NAME_DEFAULT_BILLING;
        $this->assertEquals($this->_customerData['default_billing'], $row[$billingColumn]);

        $shippingColumn = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_NAME_DEFAULT_SHIPPING;
        $this->assertEquals($this->_customerData['default_shipping'], $row[$shippingColumn]);

        $idColumn = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID;
        $this->assertEquals($this->_addressData['id'], $row[$idColumn]);

        $emailColumn = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_EMAIL;
        $this->assertEquals($this->_customerData['email'], $row[$emailColumn]);

        $websiteColumn = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_WEBSITE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$websiteColumn]);

        $this->assertEquals($this->_addressData[self::ATTRIBUTE_CODE], $row[self::ATTRIBUTE_CODE]);
    }
}
