<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Rma_Helper_EavTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Rma_Helper_Eav
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeConfigMock;

    protected function setUp()
    {
        $this->_attributeConfigMock = $this->getMock('Magento_Eav_Model_Entity_Attribute_Config', array(), array(), '', false);
        $this->_model = new Magento_Rma_Helper_Eav(
            $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false, false),
            $this->_attributeConfigMock
        );
    }

    /**
     * @param array $attributeValidateRules
     * @param array $additionalClasses
     * @dataProvider getAdditionalTextElementClassesDataProvider
     */
    public function testGetAdditionalTextElementClasses($validateRules, $additionalClasses)
    {
        $attributeMock = new Magento_Object(
            array('validate_rules' => $validateRules)
        );
        $this->assertEquals($this->_model->getAdditionalTextElementClasses($attributeMock), $additionalClasses);
    }

    public function getAdditionalTextElementClassesDataProvider()
    {
        return array(
            array(
                array(),
                array()
            ),
            array(
                array('min_text_length' => 10),
                array('validate-length', 'minimum-length-10')
            ),
            array(
                array('max_text_length' => 20),
                array('validate-length', 'maximum-length-20')
            ),
            array(
                array('min_text_length' => 10, 'max_text_length' => 20),
                array('validate-length', 'minimum-length-10', 'maximum-length-20')
            ),
        );
    }
}
