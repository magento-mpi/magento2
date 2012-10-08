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
     * Class name
     *
     * @var string
     */
    protected $_className = 'TestClassName';

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
     * Object for create method
     *
     * @var string
     */
    protected $_objectCreate = 'TestObjectCreate';

    /**
     * Object for get method
     *
     * @var string
     */
    protected $_objectGet = 'TestObjectGet';

    /**
     * Create Magento_ObjectManager_Zend instance
     *
     * @param bool $mockNewInstance
     */
    protected function _prepareObjectManagerForTests($mockNewInstance = false)
    {
        $instanceManager = $this->getMock('Zend\Di\InstanceManager', array(), array(), '', false);

        $this->_config = $this->getMock('Mage_Core_Model_Config', array('loadBase'), array(), '', false);
        $this->_config->expects($this->any())
            ->method('loadBase')
            ->will($this->returnValue($this->_config));

        $diInstance = $this->getMock('Zend\Di\Di', array('instanceManager', 'newInstance', 'get'),
            array(null, $instanceManager, null));
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

    /**
     * Test for create method
     *
     * @covers Magento_ObjectManager_Zend::create
     */
    public function testCreate()
    {
        $this->_prepareObjectManagerForTests(true);
        $actualObject = $this->_model->create($this->_className, $this->_arguments);
        $this->assertEquals($this->_objectCreate, $actualObject);
    }

    /**
     * Test for get method
     *
     * @covers Magento_ObjectManager_Zend::get
     */
    public function testGet()
    {
        $this->_prepareObjectManagerForTests();
        $actualObject = $this->_model->get($this->_className, $this->_arguments);
        $this->assertEquals($this->_objectGet, $actualObject);
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
        $this->assertEquals($this->_className, $className);
        $this->assertEquals($this->_arguments, $arguments);

        return $this->_objectCreate;
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

        $this->assertEquals($this->_className, $className);
        $this->assertEquals($this->_arguments, $arguments);

        return $this->_objectGet;
    }
}