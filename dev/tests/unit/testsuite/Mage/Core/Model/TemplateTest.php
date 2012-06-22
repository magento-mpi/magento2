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
    /**
     * Template mock
     *
     * @var Mage_Core_Model_Template
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Mage_Core_Model_Template', array(array(
            'area' => Mage_Core_Model_App_Area::AREA_FRONTEND,
            'store' => 1
        )));
    }

    /**
     * @param array $config
     * @expectedException Magento_Exception
     * @dataProvider invalidInputParametersDataProvider
     */
    public function testSetDesignConfigWithInvalidInputParametersThrowsException($config)
    {
        $this->_model->setDesignConfig($config);
    }

    public function testSetDesignConfigWithValidInputParametersReturnsSuccess()
    {
        $config = array(
            'area' => 'some_area',
            'store' => 1
        );
        $this->_model->setDesignConfig($config);
        $this->assertEquals($config, $this->_model->getDesignConfig()->getData());
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
