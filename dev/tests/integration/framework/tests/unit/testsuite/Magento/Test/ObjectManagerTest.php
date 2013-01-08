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
        $resource = new stdClass;

        $instanceManager = new Magento_Test_Di_InstanceManager();
        $instanceManager->addSharedInstance($resource, 'Mage_Core_Model_Resource');

        $diInstance = new Zend\Di\Di();
        $model = new Magento_Test_ObjectManager(null, $diInstance);

        $diInstance->setInstanceManager($instanceManager);
        $this->assertSame($model, $model->clearCache());
        $this->assertNotSame($instanceManager, $diInstance->instanceManager());
        $this->assertSame($model, $diInstance->instanceManager()->getSharedInstance('Magento_ObjectManager'));
        $this->assertSame($resource, $diInstance->instanceManager()->getSharedInstance('Mage_Core_Model_Resource'));
        $this->assertFalse($diInstance->instanceManager()->hasSharedInstance('sharedInstance'));
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
        $diInstance      = $this->getMock('Zend\Di\Di', array('instanceManager'));
        $instanceManager = $this->getMock(
            'Magento_Test_Di_InstanceManager', array('addSharedInstance'), array(), '', false
        );

        $instanceManager->expects($this->exactly(2))
            ->method('addSharedInstance');
        $instanceManager->expects($this->at(1))
            ->method('addSharedInstance')
            ->with($instance, $classOrAlias);
        $diInstance->expects($this->exactly(2))
            ->method('instanceManager')
            ->will($this->returnValue($instanceManager));

        $this->_model = new Magento_Test_ObjectManager(null, $diInstance);
    }

    public function testRemoveSharedInstance()
    {
        $alias = 'Varien_Object_Alias';

        $this->_prepareObjectManagerForRemoveSharedInstance($alias);
        $this->_model->removeSharedInstance($alias);
    }

    /**
     * Prepare all required mocks for removeSharedInstance
     *
     * @param string $classOrAlias
     */
    protected function _prepareObjectManagerForRemoveSharedInstance($classOrAlias)
    {
        $diInstance      = $this->getMock('Zend\Di\Di', array('instanceManager'));
        $instanceManager = $this->getMock(
            'Magento_Test_Di_InstanceManager', array('removeSharedInstance'), array(), '', false
        );

        $instanceManager->expects($this->once())
            ->method('removeSharedInstance')
            ->with($classOrAlias);
        $diInstance->expects($this->exactly(2))
            ->method('instanceManager')
            ->will($this->returnValue($instanceManager));

        $this->_model = new Magento_Test_ObjectManager(null, $diInstance);
    }
}
