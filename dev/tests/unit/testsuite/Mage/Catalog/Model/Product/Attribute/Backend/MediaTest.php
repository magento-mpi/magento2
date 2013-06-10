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

class Mage_Catalog_Model_Product_Attribute_Backend_MediaTest extends PHPUnit_Framework_TestCase
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
        $this->_resource = $this->getMock('StdClass', array('getMainTable'));
        $this->_resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue('table'));

        $this->_mediaConfig = $this->getMock('Mage_Catalog_Model_Product_Media_Config', array(), array(), '', false);
        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $this->_model = new Mage_Catalog_Model_Product_Attribute_Backend_Media(
            $this->_mediaConfig,
            $this->_dirs,
            $this->_filesystem,
            array('resourceModel' => $this->_resource)
        );
    }

    public function testGetAffectedFields()
    {
        $valueId = 2345;
        $attributeId = 345345;

        $attribute = $this->getMock(
            'Mage_Eav_Model_Entity_Attribute_Abstract',
            array('getBackendTable', 'isStatic', 'getAttributeId', 'getName'),
            array(),
            '',
            false
        );
        $attribute->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('image'));

        $attribute->expects($this->any())
            ->method('getAttributeId')
            ->will($this->returnValue($attributeId));

        $attribute->expects($this->any())
            ->method('isStatic')
            ->will($this->returnValue(false));

        $attribute->expects($this->any())
            ->method('getBackendTable')
            ->will($this->returnValue('table'));


        $this->_model->setAttribute($attribute);

        $object = new Varien_Object();
        $object->setImage(array(
            'images' => array(array(
                'value_id' => $valueId
            ))
        ));
        $object->setId(555);

        $this->assertEquals(
            array(
                'table' => array(array(
                    'value_id' => $valueId,
                    'attribute_id' => $attributeId,
                    'entity_id' => $object->getId(),
                ))
            ),
            $this->_model->getAffectedFields($object)
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
                    $attributeCode => array(
                        'images' => array(
                            array('file' => 'file'),
                            array('file' => 'file'),
                        ),
                    ),
                ),
                $attributeCode,
            ),
            array(
                array(
                    $attributeCode => array(
                        'images' => array(
                        ),
                    ),
                ),
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
        $filesystem->expects($this->exactly(count($data[$attributeCode]['images'])))->method('delete');

        $model = new Mage_Catalog_Model_Product_Attribute_Backend_Media(
            $this->_mediaConfig,
            $this->_dirs,
            $filesystem,
            array('resourceModel' => $this->_resource)
        );

        $attribute->expects($this->any())->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        $model->setAttribute($attribute);


        $model->afterDelete($imageObject);
    }
}
