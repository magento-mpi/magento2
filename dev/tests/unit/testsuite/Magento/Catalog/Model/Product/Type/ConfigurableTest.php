<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Catalog_Model_Product_Type_ConfigurableTest
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Product_Type_ConfigurableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Type_Configurable
     */
    protected $_model;

    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectHelper;

    protected function setUp()
    {
        $this->_objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $coreDataMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $fileStorageDbMock = $this->getMock('Magento_Core_Helper_File_Storage_Database', array(), array(), '', false);
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $productFactoryMock = $this->getMock('Magento_Catalog_Model_ProductFactory', array(), array(), '', false);
        $configurableFactoryMock = $this->getMock('Magento_Catalog_Model_Resource_Product_Type_ConfigurableFactory',
            array(), array(), '', false);
        $entityFactoryMock = $this->getMock('Magento_Eav_Model_EntityFactory', array(), array(), '', false);
        $setFactoryMock = $this->getMock('Magento_Eav_Model_Entity_Attribute_SetFactory', array(), array(), '', false);
        $attributeFactoryMock = $this->getMock('Magento_Catalog_Model_Resource_Eav_AttributeFactory', array(),
            array(), '', false);
        $confAttributeFactoryMock = $this->getMock('Magento_Catalog_Model_Product_Type_Configurable_AttributeFactory',
            array(), array(), '', false);
        $productCollectionFactory = $this->getMock(
            'Magento_Catalog_Model_Resource_Product_Type_Configurable_Product_CollectionFactory',
            array(), array(), '', false
        );
        $attributeCollectionFactory = $this->getMock(
            'Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_CollectionFactory',
            array(), array(), '', false
        );
        $this->_model = $this->_objectHelper->getObject('Magento_Catalog_Model_Product_Type_Configurable', array(
            'productFactory' => $productFactoryMock,
            'typeConfigurableFactory' => $configurableFactoryMock,
            'entityFactory' => $entityFactoryMock,
            'attributeSetFactory' => $setFactoryMock,
            'eavAttributeFactory' => $attributeFactoryMock,
            'configurableAttributeFactory' => $confAttributeFactoryMock,
            'productCollectionFactory' => $productCollectionFactory,
            'attributeCollectionFactory' => $attributeCollectionFactory,
            'eventManager' => $eventManager,
            'coreData' => $coreDataMock,
            'fileStorageDb' => $fileStorageDbMock,
            'filesystem' => $filesystem,
            'coreRegistry' => $coreRegistry,
            'logger' => $logger
        ));
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
