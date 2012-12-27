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
     * Expected instance manager parametrized cache after clear
     *
     * @var array
     */
    protected $_instanceCache = array(
        'hashShort' => array(),
        'hashLong'  => array()
    );

    public function testClearCache()
    {
        $object = $this->getMock('stdClass', array('__destruct'));
        $object
            ->expects($this->once())
            ->method('__destruct')
        ;

        $resource = $this->getMock('stdClass', array('__destruct'));
        $object
            ->expects($this->once())
            ->method('__destruct')
        ;

        $instanceManager = new Magento_Di_InstanceManager_Zend();
        $instanceManager->addSharedInstance($object, 'sharedInstance');
        $instanceManager->addSharedInstance($resource, 'Mage_Core_Model_Resource');

        $diInstance = new Magento_Di_Zend();
        $model = new Magento_Test_ObjectManager(null, $diInstance);

        // Reflection is the only way to setup fixture input data in place of the hard-coded property value
        $reflectionClass = new ReflectionProperty(get_class($model), '_classesToDestruct');
        $reflectionClass->setAccessible(true);
        $reflectionClass->setValue($model, array('sharedInstance', 'nonRegisteredInstance'));

        $diInstance->setInstanceManager($instanceManager);
        $this->assertSame($model, $model->clearCache());
        $this->assertNotSame($instanceManager, $diInstance->instanceManager());
        $this->assertSame($model, $diInstance->instanceManager()->getSharedInstance('Magento_ObjectManager'));
        $this->assertSame($resource, $diInstance->instanceManager()->getSharedInstance('Mage_Core_Model_Resource'));
        $this->assertFalse($diInstance->instanceManager()->hasSharedInstance('sharedInstance'));
    }
}
