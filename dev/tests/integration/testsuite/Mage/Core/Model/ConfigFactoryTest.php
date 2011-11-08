<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Second part of Mage_Core_Model_Config testing:
 * - Mage factory behaviour is tested
 *
 * @group module:Mage_Core
 * @see Mage_Core_Model_ConfigTest
 */
class Mage_Core_Model_ConfigFactoryTest extends PHPUnit_Framework_TestCase
{
    protected static $_options = array();

    /** @var Mage_Core_Model_Config */
    protected $_model;

    public static function setUpBeforeClass()
    {
        self::$_options = Magento_Test_Bootstrap::getInstance()->getAppOptions();
    }

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Config;
        $this->_model->init(self::$_options);
    }

    /**
     * @dataProvider classNameRewriteDataProvider
     */
    public function testClassNameRewrite($originalClass, $expectedClass, $classNameGetter)
    {
        $this->_model->setNode("global/rewrites/$originalClass", $expectedClass);
        $this->assertEquals($expectedClass, $this->_model->$classNameGetter($originalClass));
    }

    public function classNameRewriteDataProvider()
    {
        return array(
            'block'          => array('My_Module_Block_Class', 'Another_Module_Block_Class', 'getBlockClassName'),
            'helper'         => array('My_Module_Helper_Data', 'Another_Module_Helper_Data', 'getHelperClassName'),
            'model'          => array('My_Module_Model_Class', 'Another_Module_Model_Class', 'getModelClassName'),
            'resource model' => array(
                'My_Module_Model_Resource_Collection',
                'Another_Module_Model_Resource_Collection_New',
                'getResourceModelClassName'
            ),
        );
    }

    public function testGetBlockClassName()
    {
        $this->assertEquals('Mage_Core_Block_Template', $this->_model->getBlockClassName('Mage_Core_Block_Template'));
    }

    /**
     * @dataProvider getHelperClassNameDataProvider
     */
    public function testGetHelperClassName($inputClassName, $expectedClassName)
    {
        $this->assertEquals($expectedClassName, $this->_model->getHelperClassName($inputClassName));
    }

    public function getHelperClassNameDataProvider()
    {
        return array(
            'class name'  => array('Mage_Core_Helper_Http', 'Mage_Core_Helper_Http'),
            'module name' => array('Mage_Core',             'Mage_Core_Helper_Data'),
        );
    }

    public function testGetResourceHelper()
    {
        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Helper_Abstract', $this->_model->getResourceHelper('Mage_Core')
        );
    }

    public function testGetModelClassName()
    {
        $this->assertEquals('Mage_Core_Model_Config', $this->_model->getModelClassName('Mage_Core_Model_Config'));
    }

    public function testGetModelInstance()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config', $this->_model->getModelInstance('Mage_Core_Model_Config'));
    }

    public function testGetResourceModelClassName()
    {
        $this->assertEquals(
            'Mage_Core_Model_Resource_Config',
            $this->_model->getResourceModelClassName('Mage_Core_Model_Resource_Config')
        );
    }

    public function testGetResourceModelInstance()
    {
        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Config',
            $this->_model->getResourceModelInstance('Mage_Core_Model_Resource_Config')
        );
    }
}
