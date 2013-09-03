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
 * Test class for Magento_Catalog_Model_Entity_Attribute_Set
 */
class Magento_Catalog_Model_Resource_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get attribute list
     *
     * @return array
     */
    protected function _getAttributes()
    {
        $attributes = array();
        $codes = array('entity_type_id', 'attribute_set_id', 'created_at', 'updated_at', 'parent_id', 'increment_id');
        foreach ($codes as $code) {
            $mock = $this->getMock(
                'Magento_Eav_Model_Entity_Attribute_Abstract',
                array('isInSet', 'getBackend'),
                array(),
                '',
                false
            );

            $mock->setAttributeId($code);
            $mock->setAttributeCode($code);

            $mock->expects($this->once())
                ->method('isInSet')
                ->will($this->returnValue(false));

            $attributes[$code] = $mock;
        }
        return $attributes;
    }


    public function testWalkAttributes()
    {
        $code = 'test_attr';
        $set = 10;

        $object = $this->getMock('Magento_Catalog_Model_Product', null, array(), '', false);

        $object->setData(array(
            'test_attr' => 'test_attr',
            'attribute_set_id' => $set,
        ));

        $entityType = new \Magento\Object();
        $entityType->setEntityTypeCode('test');
        $entityType->setEntityTypeId(0);
        $entityType->setEntityTable('table');

        $attributes = $this->_getAttributes();

        $attribute = $this->getMock(
            'Magento_Eav_Model_Entity_Attribute_Abstract',
            array('isInSet', 'getBackend'),
            array(),
            '',
            false
        );
        $attribute->setAttributeId($code);
        $attribute->setAttributeCode($code);

        $attribute->expects($this->once())
            ->method('isInSet')
            ->with($this->equalTo($set))
            ->will($this->returnValue(false));

        $attributes[$code] = $attribute;


        /** @var $model Magento_Catalog_Model_Resource_Abstract */
        $model = $this->getMock('Magento_Catalog_Model_Resource_Abstract', null, array(array(
            'type' => $entityType,
            'entityTable' => 'entityTable',
            'attributesByCode' => $attributes,
        )));

        $model->walkAttributes('backend/afterSave', array($object));
    }
}
