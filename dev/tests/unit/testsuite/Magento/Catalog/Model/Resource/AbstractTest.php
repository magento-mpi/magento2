<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Test class for \Magento\Catalog\Model\Entity\Attribute\Set
 */
namespace Magento\Catalog\Model\Resource;

class AbstractTest extends \PHPUnit_Framework_TestCase
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
                'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
                array('isInSet', 'getBackend', '__wakeup'),
                array(),
                '',
                false
            );

            $mock->setAttributeId($code);
            $mock->setAttributeCode($code);

            $mock->expects($this->once())->method('isInSet')->will($this->returnValue(false));

            $attributes[$code] = $mock;
        }
        return $attributes;
    }

    public function testWalkAttributes()
    {
        $code = 'test_attr';
        $set = 10;

        $object = $this->getMock('Magento\Catalog\Model\Product', array('__wakeup'), array(), '', false);

        $object->setData(array('test_attr' => 'test_attr', 'attribute_set_id' => $set));

        $entityType = new \Magento\Framework\Object();
        $entityType->setEntityTypeCode('test');
        $entityType->setEntityTypeId(0);
        $entityType->setEntityTable('table');

        $attributes = $this->_getAttributes();

        $attribute = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array('isInSet', 'getBackend', '__wakeup'),
            array(),
            '',
            false
        );
        $attribute->setAttributeId($code);
        $attribute->setAttributeCode($code);

        $attribute->expects(
            $this->once()
        )->method(
            'isInSet'
        )->with(
            $this->equalTo($set)
        )->will(
            $this->returnValue(false)
        );

        $attributes[$code] = $attribute;

        /** @var $model \Magento\Catalog\Model\Resource\AbstractResource */
        $model = $this->getMock(
            'Magento\Catalog\Model\Resource\AbstractResource',
            array('getAttributesByCode'),
            array(
                $this->getMock('Magento\Framework\App\Resource', array(), array(), '', false, false),
                $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false, false),
                $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', array(), array(), '', false, false),
                $this->getMock('Magento\Framework\Locale\FormatInterface'),
                $this->getMock('Magento\Eav\Model\Resource\Helper', array(), array(), '', false, false),
                $this->getMock('Magento\Framework\Validator\UniversalFactory', array(), array(), '', false, false),
                $this->getMock('Magento\Store\Model\StoreManagerInterface', array(), array(), '', false),
                $this->getMock('Magento\Catalog\Model\Factory', array(), array(), '', false),
                array()
            )
        );

        $model->expects($this->once())->method('getAttributesByCode')->will($this->returnValue($attributes));

        $model->walkAttributes('backend/afterSave', array($object));
    }
}
