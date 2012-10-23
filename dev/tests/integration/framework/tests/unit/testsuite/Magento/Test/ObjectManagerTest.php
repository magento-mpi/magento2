<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ObjectManager_Zend
 */
class Magento_Test_ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * ObjectManager instance for tests
     *
     * @var Magento_Test_ObjectManager
     */
    protected $_model;

    /**
     * Expected instance manager parametrized cache after clear
     *
     * @var array
     */
    protected $_instanceCache = array(
        'hashShort' => array(),
        'hashLong'  => array()
    );

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testClearCache()
    {
        $this->_prepareObjectManagerForClearCache();
        $this->_model->clearCache();
    }

    /**
     * Prepare all required mocks for clearCache
     */
    protected function _prepareObjectManagerForClearCache()
    {
        $diInstance      = $this->getMock('Zend\Di\Di', array('get', 'instanceManager', 'setInstanceManager'));
        $instanceManager = $this->getMock('Zend\Di\InstanceManager', array(), array(), '', false);

        $diInstance->expects($this->exactly(3))
            ->method('instanceManager')
            ->will($this->returnValue($instanceManager));
        $diInstance->expects($this->exactly(5))
            ->method('get')
            ->will($this->returnCallback(array($this, 'getCallback')));
        $diInstance->expects($this->once())
            ->method('setInstanceManager')
            ->will($this->returnCallback(array($this, 'verifySetInstanceManager')));

        $this->_model = new Magento_Test_ObjectManager(null, $diInstance);
    }

    /**
     * Callback method for Zend\Di\Di::get
     *
     * @param $className
     * @param array $arguments
     * @return null|PHPUnit_Framework_MockObject_MockObject
     */
    public function getCallback($className, array $arguments = array())
    {
        $this->assertEmpty($arguments);
        if ($className == 'Mage_Core_Model_Config') {
            return $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        } elseif ($className == 'Mage_Core_Model_Resource') {
            return $this->getMock('Mage_Core_Model_Resource', array(), array(), '', false);
        }
        return null;
    }

    /**
     * Callback method for Zend\Di\Di::setInstanceManager
     *
     * @param \Zend\Di\InstanceManager $instanceManager
     */
    public function verifySetInstanceManager($instanceManager)
    {
        $this->assertInstanceOf('Zend\Di\InstanceManager', $instanceManager);
        $this->assertAttributeEmpty('sharedInstances', $instanceManager);
        $this->assertAttributeEquals($this->_instanceCache, 'sharedInstancesWithParams', $instanceManager);
    }

    public function testAddSharedInstance()
    {
        $object = new Varien_Object();
        $alias  = 'Varien_Object_Alias';

        $this->_prepareObjectManagerForAddSharedInstance($object, $alias);
        $this->_model->addSharedInstance($object, $alias);
    }

    /**
     * Prepare all required mocks for addSharedInstance
     *
     * @param object $instance
     * @param string $classOrAlias
     */
    protected function _prepareObjectManagerForAddSharedInstance($instance, $classOrAlias)
    {
        $diInstance      = $this->getMock('Zend\Di\Di', array('get', 'instanceManager'));
        $magentoConfig   = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $instanceManager = $this->getMock('Zend\Di\InstanceManager', array('addSharedInstance'), array(), '', false);

        $instanceManager->expects($this->exactly(2))
            ->method('addSharedInstance');
        $instanceManager->expects($this->at(1))
            ->method('addSharedInstance')
            ->with($instance, $classOrAlias);
        $diInstance->expects($this->exactly(2))
            ->method('instanceManager')
            ->will($this->returnValue($instanceManager));
        $diInstance->expects($this->exactly(2))
            ->method('get')
            ->with('Mage_Core_Model_Config')
            ->will($this->returnValue($magentoConfig));

        $this->_model = new Magento_Test_ObjectManager(null, $diInstance);
    }
}
