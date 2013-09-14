<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Eav_Model_Entity_Attribute_Backend_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend',
            array(),
            '',
            false
        );
    }

    public function testGetAffectedFields()
    {
        $valueId = 10;
        $attributeId = 42;

        $attribute = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array('getBackendTable', 'isStatic', 'getAttributeId'),
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

        $this->_model->setAttribute($attribute);

        $object = new \Magento\Object();
        $this->_model->setValueId($valueId);

        $this->assertEquals(
            array(
                'table' => array(array(
                    'value_id' => $valueId,
                    'attribute_id' => $attributeId
                ))
            ),
            $this->_model->getAffectedFields($object)
        );
    }
}
