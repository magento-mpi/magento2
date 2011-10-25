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

    public function testGetGroupedClassName()
    {
        $this->assertEquals('Mage_Core_Model_Config', $this->_model->getGroupedClassName('model', 'core/config'));
        $this->assertEquals('Mage_Core_Block_Config', $this->_model->getGroupedClassName('block', 'core/config'));
        $this->assertEquals('Mage_Core_Helper_String', $this->_model->getGroupedClassName('helper', 'core/string'));
    }

    public function testGetGroupedClassNameRewrites()
    {
        $rewritingClass = 'Some_Class';
        $this->_model->setNode('global/models/a_module/rewrite/a_model', $rewritingClass);
        $this->assertEquals($rewritingClass, $this->_model->getGroupedClassName('model', 'a_module/a_model'));

        $rewrittenClass = 'Some_Class_To_Rewrite';
        $rewritingClass = 'Some_Class_That_Rewrites';
        $this->_model->setNode('global/rewrites/' . $rewrittenClass, $rewritingClass);
        $this->assertEquals($rewritingClass, $this->_model->getGroupedClassName(null, $rewrittenClass));
    }

    public function testGetBlockClassName()
    {
        $this->assertEquals('Mage_Core_Block_Config', $this->_model->getBlockClassName('core/config'));
        $this->assertEquals('Mage_Core_Block_Template', $this->_model->getBlockClassName('Mage_Core_Block_Template'));
    }

    public function testGetHelperClassName()
    {
        $this->assertEquals('Mage_Core_Helper_Data', $this->_model->getHelperClassName('core'));
        $this->assertEquals('Mage_Core_Helper_String', $this->_model->getHelperClassName('core/string'));
        $this->assertEquals('Mage_Core_Helper_Http', $this->_model->getHelperClassName('Mage_Core_Helper_Http'));
    }

    public function testGetResourceHelper()
    {
        $this->assertInstanceOf('Mage_Core_Model_Resource_Helper_Abstract', $this->_model->getResourceHelper('core'));
        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Helper_Abstract', $this->_model->getResourceHelper('catalog')
        );
    }

    public function testGetModelClassName()
    {
        $this->assertEquals('Mage_Core_Model_Config', $this->_model->getModelClassName('core/config'));
    }

    public function testGetModelInstance()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config', $this->_model->getModelInstance('core/config'));
        $this->assertInstanceOf(
            'Mage_Core_Model_Config', $this->_model->getModelInstance('Mage_Core_Model_Config')
        );
    }

    /**
     *
     * @param string $configPath
     * @param string $node
     * @param string $nodeValue
     * @param string $expectedClass
     *
     * @dataProvider getNodeClassInstanceDataProvider
     */
    public function testGetNodeClassInstance($configPath, $node, $nodeValue, $expectedClass)
    {
        $nodePath = $configPath . '/' . $node;
        $oldValue = Mage::getConfig()->getNode($nodePath);

        Mage::getConfig()->setNode($nodePath, $nodeValue);
        $actualInstance = $this->_model->getNodeClassInstance($configPath);
        Mage::getConfig()->setNode($nodePath, $oldValue);

        $this->assertInstanceOf($expectedClass, $actualInstance);
    }

    /**
     * @return array
     */
    public function getNodeClassInstanceDataProvider()
    {
        return array(
            'alias in "class"' => array(
                'global/tmp/alias_class',
                'class',
                'core/url',
                'Mage_Core_Model_Url'
            ),
            'alias in "model"' => array(
                'global/tmp/alias_model',
                'model',
                'core/url',
                'Mage_Core_Model_Url'
            ),
            'full class in "class"' => array(
                'global/tmp/full_class',
                'class',
                'Mage_Core_Model_Url',
                'Mage_Core_Model_Url'
            ),
            'full class in "model"' => array(
                'global/tmp/full_model',
                'model',
                'Mage_Core_Model_Url',
                'Mage_Core_Model_Url'
            ),
        );
    }

    public function testGetResourceModelClassName()
    {
        $this->assertEquals(
            'Mage_Core_Model_Resource_Config', $this->_model->getResourceModelClassName('core/config')
        );
    }

    public function testGetResourceModelInstance()
    {
        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Config', $this->_model->getResourceModelInstance('core/config')
        );
        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Config',
            $this->_model->getResourceModelInstance('Mage_Core_Model_Resource_Config')
        );
    }
}
