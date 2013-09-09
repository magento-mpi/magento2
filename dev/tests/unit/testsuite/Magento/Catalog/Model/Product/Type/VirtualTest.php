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

class Magento_Catalog_Model_Product_Type_VirtualTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Type_Virtual
     */
    protected $_model;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $coreDataMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $fileStorageDbMock = $this->getMock('Magento_Core_Helper_File_Storage_Database', array(), array(), '', false);
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $this->_model = new Magento_Catalog_Model_Product_Type_Virtual(
            $eventManager,
            $coreDataMock,
            $fileStorageDbMock,
            $filesystem
        );
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }
}
