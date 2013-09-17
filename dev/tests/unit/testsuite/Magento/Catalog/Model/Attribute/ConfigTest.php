<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Attribute_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Attribute_Config
     */
    protected $_model;

    /**
     * @var Magento_Catalog_Model_Attribute_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataStorage;

    protected function setUp()
    {
        $this->_dataStorage = $this->getMock(
            'Magento_Catalog_Model_Attribute_Config_Data', array('getData'), array(), '', false
        );
        $this->_model = new Magento_Catalog_Model_Attribute_Config($this->_dataStorage);
    }

    public function testGetAttributeNames()
    {
        $expectedResult = array(
            'fixture_attribute_one',
            'fixture_attribute_two',
        );
        $this->_dataStorage
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(array(
                'some_group' => $expectedResult,
            )))
        ;
        $this->assertSame($expectedResult, $this->_model->getAttributeNames('some_group'));
    }
}
