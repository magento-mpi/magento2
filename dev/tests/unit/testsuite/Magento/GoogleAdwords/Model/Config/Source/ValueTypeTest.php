<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GoogleAdwords_Model_Config_Source_ValueTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GoogleAdwords\Model\Config\Source\ValueType
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('\Magento\GoogleAdwords\Model\Config\Source\ValueType', array());
    }

    public function testToOptionArray()
    {
        $this->assertEquals(array(
            array(
                'value' => \Magento\GoogleAdwords\Helper\Data::CONVERSION_VALUE_TYPE_DYNAMIC,
                'label' => 'Dynamic',
            ),
            array(
                'value' => \Magento\GoogleAdwords\Helper\Data::CONVERSION_VALUE_TYPE_CONSTANT,
                'label' => 'Constant',
            ),
        ), $this->_model->toOptionArray());
    }
}
