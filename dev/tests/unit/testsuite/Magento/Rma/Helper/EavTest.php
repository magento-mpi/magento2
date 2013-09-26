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

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $collectionFactory = $this->getMock('Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory',
            array('create'), array(), '', false);
        $attributeConfig = $this->getMock('Magento_Eav_Model_Entity_Attribute_Config',
            array(), array(), '', false);
        $this->_model = $helper->getObject('Magento_Rma_Helper_Eav', array(
            'collectionFactory' => $collectionFactory,
            'attributeConfig' => $attributeConfig,
            'context' => $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false)
        ));
    }

    /**
     * @param $validateRules
     * @param array $additionalClasses
     * @internal param array $attributeValidateRules
     * @dataProvider getAdditionalTextElementClassesDataProvider
     */
    public function testGetAdditionalTextElementClasses($validateRules, $additionalClasses)
    {
        $attributeMock = new Magento_Object(
            array('validate_rules' => $validateRules)
        );
        $this->assertEquals($this->_model->getAdditionalTextElementClasses($attributeMock), $additionalClasses);
    }

    /**
     * @return array
     */
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
