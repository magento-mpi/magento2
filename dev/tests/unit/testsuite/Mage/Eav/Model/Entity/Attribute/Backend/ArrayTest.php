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
     * @var Mage_Eav_Model_Entity_Attribute_Backend_Array
     */
    protected $_model;

    /**
     * @var Mage_Eav_Model_Entity_Attribute
     */
    protected $_attribute;

    protected function setUp()
    {
        $this->_attribute = $this->getMock(
            'Mage_Eav_Model_Entity_Attribute', array('getAttributeCode'), array(), '', false
        );
        $this->_model = new Mage_Eav_Model_Entity_Attribute_Backend_Array();
        $this->_model->setAttribute($this->_attribute);
    }

    /**
     * @dataProvider attributeValueDataProvider
     */
    public function testValidate($data)
    {
        $this->_attribute->expects($this->atLeastOnce())->method('getAttributeCode')->will($this->returnValue('code'));
        $product = new Varien_Object(array('code' => $data));
        $this->_model->validate($product);
        $this->assertEquals('1,2,3', $product->getCode());
    }

    public static function attributeValueDataProvider()
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
