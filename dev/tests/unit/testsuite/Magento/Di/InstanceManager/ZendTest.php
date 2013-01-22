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
 * Test class for Magento_Di_InstanceManager_Zend
 */
class Magento_Di_InstanceManager_ZendTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test object alias
     */
    const TEST_ALIAS = 'test_alias';

    /**
     * Shared instances attribute name
     */
    const SHARED_ATTRIBUTE = 'sharedInstances';

    /**
     * Test class name
     */
    const TEST_CLASS = 'Test_Class_Name';

    public function testRemoveSharedInstance()
    {
        $instanceManager = new Magento_Di_InstanceManager_Zend();
        $this->assertAttributeEmpty(self::SHARED_ATTRIBUTE, $instanceManager);

        $testObject = new Varien_Object();
        $instanceManager->addSharedInstance($testObject, self::TEST_ALIAS);
        $this->assertAttributeEquals(
            array(self::TEST_ALIAS => $testObject),
            self::SHARED_ATTRIBUTE,
            $instanceManager
        );

        $instanceManager->removeSharedInstance(self::TEST_ALIAS);
        $this->assertAttributeEmpty(self::SHARED_ATTRIBUTE, $instanceManager);
    }

    public function testAddTypePreference()
    {
        $generatorMock = $this->getMock('Magento_Di_Generator', array('generateClass'), array(), '', false);
        $generatorMock->expects($this->once())
            ->method('generateClass')
            ->with(self::TEST_CLASS);

        $instanceManager = new Magento_Di_InstanceManager_Zend($generatorMock);
        $this->assertInstanceOf(
            'Magento_Di_InstanceManager_Zend',
            $instanceManager->addTypePreference('Interface', self::TEST_CLASS)
        );
    }

    public function testSetParameters()
    {
        $generatorMock = $this->getMock('Magento_Di_Generator', array('generateClass'), array(), '', false);
        $generatorMock->expects($this->once())
            ->method('generateClass')
            ->with(self::TEST_CLASS);

        $instanceManager = new Magento_Di_InstanceManager_Zend($generatorMock);
        $instanceManager->setParameters('Class', array('parameter' => self::TEST_CLASS));
    }
}
