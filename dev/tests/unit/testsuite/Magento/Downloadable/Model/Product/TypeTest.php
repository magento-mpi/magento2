<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Downloadable_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Downloadable_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $downloadableFile = $this->getMockBuilder('Magento_Downloadable_Helper_File')
            ->disableOriginalConstructor()->getMock();
        $coreData = $this->getMockBuilder('Magento_Core_Helper_Data')->disableOriginalConstructor()->getMock();
        $fileStorageDb = $this->getMockBuilder('Magento_Core_Helper_File_Storage_Database')
            ->disableOriginalConstructor()->getMock();
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $sampleResource = $this->getMock('Magento_Downloadable_Model_Resource_Sample', array(), array(), '', false);
        $linkResource = $this->getMock('Magento_Downloadable_Model_Resource_Link', array(), array(), '', false);
        $linksFactory = $this->getMock('Magento_Downloadable_Model_Resource_Link_Collection_Factory',
            array(), array(), '', false
        );
        $samplesFactory = $this->getMock('Magento_Downloadable_Model_Resource_Sample_CollectionFactory',
            array(), array(), '', false
        );
        $sampleFactory = $this->getMock('Magento_Downloadable_Model_SampleFactory', array(), array(), '', false);
        $linkFactory = $this->getMock('Magento_Downloadable_Model_LinkFactory', array(), array(), '', false);
        $this->_model = new Magento_Downloadable_Model_Product_Type(
            $eventManager, $downloadableFile, $coreData, $fileStorageDb, $filesystem, $coreRegistry, $logger,
            $sampleResource, $linkResource, $linksFactory, $samplesFactory, $sampleFactory, $linkFactory
        );
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }
}
