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

class Magento_Catalog_Model_Product_Attribute_Backend_MediaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Attribute_Backend_Media
     */
    protected $_model;

    protected function setUp()
    {
        $resource = $this->getMock('StdClass', array('getMainTable'));
        $resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue('table'));

        $mediaConfig = $this->getMock('Magento_Catalog_Model_Product_Media_Config', array(), array(), '', false);
        $dirs = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $this->_model = new Magento_Catalog_Model_Product_Attribute_Backend_Media(
            $mediaConfig,
            $dirs,
            $filesystem,
            array('resourceModel' => $resource)
        );
    }

    public function testGetAffectedFields()
    {
        $valueId = 2345;
        $attributeId = 345345;

        $attribute = $this->getMock(
            'Magento_Eav_Model_Entity_Attribute_Abstract',
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

        $object = new Magento_Object();
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
}
