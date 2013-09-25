<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Bundle_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Bundle_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $catalogProduct = $this->getMock('Magento_Catalog_Helper_Product', array(), array(), '', false);
        $catalogData = $this->getMock('Magento_Catalog_Helper_Data', array(), array(), '', false);
        $coreData = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $fileStorageDb = $this->getMock('Magento_Core_Helper_File_Storage_Database', array(), array(), '', false);
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $bundleModelSelection = $this->getMock('Magento_Bundle_Model_SelectionFactory', array(), array(), '', false);
        $bundleFactory = $this->getMock('Magento_Bundle_Model_Resource_BundleFactory', array(), array(), '', false);
        $bundleSelectionCollection = $this->getMock(
            'Magento_Bundle_Model_Resource_Selection_CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $config = $this->getMock('Magento_Catalog_Model_Config', array(), array(), '', false);
        $bundleSelection = $this->getMock('Magento_Bundle_Model_Resource_Selection', array(), array(), '', false);
        $bundleOption = $this->getMock('Magento_Bundle_Model_OptionFactory', array(), array(), '', false);
        $storeManager = $this->getMock('Magento_Core_Model_StoreManagerInterface', array(), array(), '', false);
        $this->_model = new Magento_Bundle_Model_Product_Type(
            $bundleModelSelection,
            $bundleFactory,
            $bundleSelectionCollection,
            $config,
            $bundleSelection,
            $bundleOption,
            $storeManager,
            $eventManager,
            $catalogProduct,
            $catalogData,
            $coreData,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger
        );
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
