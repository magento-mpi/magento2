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

class Magento_Catalog_Model_Product_Attribute_Backend_Groupprice_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract
     */
    protected $_model;

    /**
     * Catalog helper
     *
     * @var Magento_Catalog_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = $this->getMock('StdClass', array('isPriceGlobal'));
        $this->_helper->expects($this->any())
            ->method('isPriceGlobal')
            ->will($this->returnValue(true));

        $this->_model = $this->getMockForAbstractClass(
            'Magento_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract',
            array(array(
                'helper' => $this->_helper
            ))
        );
        $resource = $this->getMock('StdClass', array('getMainTable'));
        $resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue('table'));

        $this->_model->expects($this->any())
            ->method('_getResource')
            ->will($this->returnValue($resource));
    }

    public function testGetAffectedFields()
    {
        $valueId = 10;
        $attributeId = 42;

        $attribute = $this->getMock(
            'Magento_Eav_Model_Entity_Attribute_Abstract',
            array('getBackendTable', 'isStatic', 'getAttributeId', 'getName'),
            array(),
            '',
            false
        );
        $attribute->expects($this->any())
            ->method('getAttributeId')
            ->will($this->returnValue($attributeId));

        $attribute->expects($this->any())
            ->method('isStatic')
            ->will($this->returnValue(false));

        $attribute->expects($this->any())
            ->method('getBackendTable')
            ->will($this->returnValue('table'));

        $attribute->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('group_price'));

        $this->_model->setAttribute($attribute);

        $object = new Magento_Object();
        $object->setGroupPrice(array(array(
            'price_id' => 10
        )));
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
