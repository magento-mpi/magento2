<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_DesignEditor_Model_Url_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\DesignEditor\Model\Url\Factory
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\DesignEditor\Model\Url\Factory($this->_objectManager);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('\Magento\ObjectManager', '_objectManager', $this->_model);
    }

    public function testReplaceClassName()
    {
        $this->_objectManager->expects($this->once())
            ->method('configure')
            ->with(array('preferences' => array('Magento\Core\Model\Url' => 'TestClass')));

        $this->assertEquals($this->_model, $this->_model->replaceClassName('TestClass'));
    }

    public function testCreate()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Core\Model\Url', array())
            ->will($this->returnValue('ModelInstance'));

        $this->assertEquals('ModelInstance', $this->_model->create());
    }
}
