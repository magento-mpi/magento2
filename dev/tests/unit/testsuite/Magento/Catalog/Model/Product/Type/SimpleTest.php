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

class Magento_Catalog_Model_Product_Type_SimpleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Type_Simple
     */
    protected $_model;

    protected function setUp()
    {
        $objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $coreDataMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $fileStorageDbMock = $this->getMock('Magento_Core_Helper_File_Storage_Database', array(), array(), '', false);
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $productFactoryMock = $this->getMock('Magento_Catalog_Model_ProductFactory', array(), array(), '', false);
        $this->_model = $objectHelper->getObject('Magento_Catalog_Model_Product_Type_Simple', array(
            'productFactory' => $productFactoryMock,
            'eventManager' => $eventManager,
            'coreData' => $coreDataMock,
            'fileStorageDb' => $fileStorageDbMock,
            'filesystem' => $filesystem,
            'coreRegistry' => $coreRegistry,
            'logger' => $logger,
        ));
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
