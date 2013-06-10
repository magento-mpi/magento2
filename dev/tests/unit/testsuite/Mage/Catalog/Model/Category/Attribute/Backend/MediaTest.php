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

class Mage_Catalog_Model_Category_Attribute_Backend_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Product_Attribute_Backend_Media
     */
    protected $_model;

    /**
     * @var Mage_Catalog_Model_Product_Media_Config
     */
    protected $_mediaConfig;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var StdClass
     */
    protected $_resource;

    protected function setUp()
    {
        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $this->_model = new Mage_Catalog_Model_Category_Attribute_Backend_Image(
            $this->_dirs,
            $this->_filesystem
        );
    }

    /**
     * Dataprovider for afterDelete test
     *
     * @return array
     */
    public function afterDeleteDataProvider()
    {
        $attributeCode = 'mediaFile';

        return array(
            array(
                array(
                    $attributeCode => 'file',
                ),
                $attributeCode,
            ),
            array(
                array(),
                $attributeCode,
            ),
        );
    }

    /**
     * Check if after attribute deletion properly calls image deletion
     *
     * @dataProvider afterDeleteDataProvider
     * @test
     */
    public function afterDelete($data, $attributeCode)
    {
        $attribute = $this->getMockForAbstractClass(
            'Mage_Eav_Model_Entity_Attribute_Abstract',
            array(),
            '',
            false,
            true,
            true,
            array(
                'getAttributeCode'
            )
        );

        $imageObject = new Varien_Object($data);

        /** @var Magento_Filesystem $filesystem */
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $count = intval(!empty($data[$attributeCode]));
        $filesystem->expects($this->exactly($count))->method('delete');

        $model = new Mage_Catalog_Model_Category_Attribute_Backend_Image(
            $this->_dirs,
            $filesystem
        );

        $attribute->expects($this->any())->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        $model->setAttribute($attribute);


        $model->afterDelete($imageObject);
    }
}
