<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_Entity_Attribute_Backend_ArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Eav_Model_Entity_Attribute_Backend_Array|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $attribute = $this->getMock('Mage_Eav_Model_Entity_Attribute', array('getAttributeCode'), array(), '', false);
        $attribute->expects($this->atLeastOnce())->method('getAttributeCode')->will($this->returnValue('code'));
        $this->_model = $this->getMock('Mage_Eav_Model_Entity_Attribute_Backend_Array', array('getAttribute'));
        $this->_model->expects($this->atLeastOnce())->method('getAttribute')->will($this->returnValue($attribute));
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate($data)
    {
        $product = new Varien_Object(array('code' => $data));
        $this->_model->validate($product);
        $this->assertEquals('1,2,3', $product->getCode());
    }

    public static function validateDataProvider()
    {
        return array(
            array(
                array(1, 2, 3)
            ),
            array(
                '1,2,3'
            )
        );
    }
}
