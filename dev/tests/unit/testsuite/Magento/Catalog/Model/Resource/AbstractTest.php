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

        $object = $this->getMock('Magento\Catalog\Model\Product', null, array(), '', false);

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
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
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

        /** @var $model \Magento\Catalog\Model\Resource\AbstractResource */
        $model = $this->getMock(
            'Magento\Catalog\Model\Resource\AbstractResource',
            array('getAttributesByCode'),
            array(
                $this->getMock('Magento\App\Resource', array(), array(), '', false, false),
                $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false, false),
                $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', array(), array(), '', false, false),
                $this->getMock('Magento\Core\Model\LocaleInterface'),
                $this->getMock('Magento\Eav\Model\Resource\Helper', array(), array(), '', false, false),
                $this->getMock('Magento\Validator\UniversalFactory', array(), array(), '', false, false),
                $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false),
                $this->getMock('Magento\Catalog\Model\Factory', array(), array(), '', false),
                array(),
            )
        );

        $model->expects($this->once())
            ->method('getAttributesByCode')
            ->will($this->returnValue($attributes));

        $model->walkAttributes('backend/afterSave', array($object));
    }
}
