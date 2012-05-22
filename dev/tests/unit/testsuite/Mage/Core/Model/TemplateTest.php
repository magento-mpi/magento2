<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Template.
 */
class Mage_Core_Model_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Mage_Core_Model_Template');
    }

    /**
     * @expectedException Magento_Exception
     * @dataProvider invalidInputParametersDataProvider
     */
    public function testSetDesignConfigWithInvalidInputParametersThrowsException($config)
    {
        $this->_model->setDesignConfig($config);
    }

    public function invalidInputParametersDataProvider()
    {
        return array(
            array(array()),
            array(array('area' => 'some_area')),
            array(array('store' => 'any_store'))
        );
    }
}
