<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_ObjectManager_PluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_ObjectManager_Pluginizer();
        $objectManager = Magento_ObjectManager_ObjectManager();
        $this->_objectManager->setObjectManager($objectManager);
    }

    public function testPluginsAreCalled()
    {
        $this->_objectManager->configure(
            array('Magento_Test_Di_Child' => array(
                'interceptors' => array(
                    'first' => 'Magento_Test_Di_Child_Interceptor_A',
                    'second' => 'Magento_Test_Di_Child_Interceptor_B'
                )
            ))
        );

        $child = $this->_objectManager->create('Magento_Test_Di_Child');
        $this->assertEquals('"(\'testString\')"', $child->testMethod('testString');
    }
}
