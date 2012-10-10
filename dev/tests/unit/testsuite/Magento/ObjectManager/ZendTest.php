<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_ZendTest extends PHPUnit_Framework_TestCase
{
    /**
     * Class name
     */
    const CLASS_NAME = 'TestClassName';

    /**#@+
     * Objects for create and get method
     */
    const OBJECT_CREATE = 'TestObjectCreate';
    const OBJECT_GET = 'TestObjectGet';
    /**#@-*/

    /**
     * Test model
     *
     * @var Magento_ObjectManager_Zend
     */
    protected $_model;

    /**
     * Config model mock
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Arguments
     *
     * @var array
     */
    protected $_arguments = array(
        'argument_1' => 'value_1',
        'argument_2' => 'value_2',
    );

    /**
     * Create Magento_ObjectManager_Zend instance
     *
     * @param bool $mockNewInstance
     */
    protected function _prepareObjectManagerForTests($mockNewInstance = false)
    {
        $this->_config = $this->getMock('Mage_Core_Model_Config', array('loadBase'), array(), '', false);
        $this->_config->expects($this->any())
            ->method('loadBase')
            ->will($this->returnSelf());

        $instanceManager = $this->getMock('Zend\Di\InstanceManager', array('addSharedInstance'), array(), '', false);
        $diInstance = $this->getMock('Zend\Di\Di', array('instanceManager', 'newInstance', 'get', 'setDefinitionList'));
        $diInstance->expects($this->any())
            ->method('instanceManager')
            ->will($this->returnValue($instanceManager));
        if ($mockNewInstance) {
            $diInstance->expects($this->once())
                ->method('newInstance')
                ->will($this->returnCallback(array($this, 'verifyCreate')));
            $diInstance->expects($this->exactly(2))
                ->method('get')
                ->will($this->returnCallback(array($this, 'verifyGet')));
        } else {
            $diInstance->expects($this->exactly(3))
                ->method('get')
                ->will($this->returnCallback(array($this, 'verifyGet')));
        }

        $this->_model = new Magento_ObjectManager_Zend(null, $diInstance);
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    public function testCreate()
    {
        $this->_prepareObjectManagerForTests(true);
        $actualObject = $this->_model->create(self::CLASS_NAME, $this->_arguments);
        $this->assertEquals(self::OBJECT_CREATE, $actualObject);
    }

    public function testGet()
    {
        $this->_prepareObjectManagerForTests();
        $actualObject = $this->_model->get(self::CLASS_NAME, $this->_arguments);
        $this->assertEquals(self::OBJECT_GET, $actualObject);
    }

    /**
     * Callback method for Zend\Di\Di::newInstance
     *
     * @param string $className
     * @param array $arguments
     * @return string
     */
    public function verifyCreate($className, array $arguments = array())
    {
        $this->assertEquals(self::CLASS_NAME, $className);
        $this->assertEquals($this->_arguments, $arguments);

        return self::OBJECT_CREATE;
    }

    /**
     * Callback method for Zend\Di\Di::get
     *
     * @param string $className
     * @param array $arguments
     * @return string|Mage_Core_Model_Config
     */
    public function verifyGet($className, array $arguments = array())
    {
        if ($className == 'Mage_Core_Model_Config') {
            return $this->_config;
        }

        $this->assertEquals(self::CLASS_NAME, $className);
        $this->assertEquals($this->_arguments, $arguments);

        return self::OBJECT_GET;
    }
}
