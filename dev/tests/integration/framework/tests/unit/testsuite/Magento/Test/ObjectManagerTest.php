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
     * Test resource value
     */
    const TEST_RESOURCE = 'test_resource';

    /**
     * ObjectManager instance for tests
     *
     * @var Magento_Test_ObjectManager
     */
    protected $_model;

    /**
     * List of classes to call __destruct() on
     *
     * @var array
     */
    protected $_classesToDestruct = array();

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
        unset($this->_classesToDestruct);
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
        $diInstance      = $this->getMock('Magento_Di_Zend', array('get', 'instanceManager', 'setInstanceManager'));
        $instanceManager = $this->getMock(
            'Magento_Di_InstanceManager_Zend', array('addSharedInstance'), array(), '', false
        );

        $diInstance->expects($this->exactly(7))
            ->method('instanceManager')
            ->will($this->returnValue($instanceManager));
        $diInstance->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnCallback(array($this, 'getCallback')));
        $diInstance->expects($this->any())
            ->method('setInstanceManager')
            ->will($this->returnSelf());

        $this->_model = new Magento_Test_ObjectManager(null, $diInstance);

        $instanceManager->expects($this->exactly(2))
            ->method('addSharedInstance');
        $instanceManager->expects($this->at(0))
            ->method('addSharedInstance')
            ->with($this->_model, 'Magento_ObjectManager');
        $instanceManager->expects($this->at(1))
            ->method('addSharedInstance')
            ->with(self::TEST_RESOURCE, 'Mage_Core_Model_Resource');
    }

    /**
     * Callback method for Magento_Di_Zend::get
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject|string
     */
    public function getCallback($className)
    {
        if ($className != 'Mage_Core_Model_Resource') {
            $this->_classesToDestruct[] = $className;
            $mock = $this->getMock($className, array('__destruct'), array(), '', false);
            return $mock;
        } else {
            return self::TEST_RESOURCE;
        }
    }
}
