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
class Mage_DesignEditor_Model_Url_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Model_Url_Factory
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager_Zend', array('addAlias', 'create'),
            array(), '', false);
        $this->_model = new Mage_DesignEditor_Model_Url_Factory($this->_objectManager);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('Magento_ObjectManager', '_objectManager', $this->_model);
    }

    public function testReplaceClassName()
    {
        $this->_objectManager->expects($this->once())
            ->method('addAlias')
            ->with('Mage_Core_Model_Url', 'TestClass');

        $this->assertEquals($this->_model, $this->_model->replaceClassName('TestClass'));
    }

    public function testCreateFromArray()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Mage_Core_Model_Url', array(), false)
            ->will($this->returnValue('ModelInstance'));

        $this->assertEquals('ModelInstance', $this->_model->createFromArray());
    }
}
