<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
require __DIR__ . '/../_files/Interface.php';
require __DIR__ . '/../_files/Parent.php';
require __DIR__ . '/../_files/Child.php';
require __DIR__ . '/../_files/Child/A.php';
require __DIR__ . '/../_files/Child/Circular.php';
require __DIR__ . '/../_files/Child/Interceptor/A.php';
require __DIR__ . '/../_files/Child/Interceptor/B.php';
require __DIR__ . '/../_files/Aggregate/Interface.php';
require __DIR__ . '/../_files/Aggregate/Parent.php';
require __DIR__ . '/../_files/Aggregate/Child.php';
require __DIR__ . '/../_files/Aggregate/WithOptional.php';
require __DIR__ . '/../_files/Child/Interceptor.php';
require_once __DIR__ . '/../_files/Child/Interceptor/A.php';
require_once __DIR__ . '/../_files/Child/Interceptor/B.php';

class Magento_ObjectManager_PluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_pluginizer;

    protected function setUp()
    {
        $config = new Magento_ObjectManager_Config();
        $this->_pluginizer = new Magento_ObjectManager_Pluginizer($config);
        $objectManager = new Magento_ObjectManager_ObjectManager($this->_pluginizer);
        $this->_pluginizer->setObjectManager($objectManager);
    }

    public function testPluginsAreCalled()
    {
        $this->_pluginizer->configure(
            array('Magento_Test_Di_Child' => array(
                'interceptors' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                )
            ))
        );

        $child = $this->_pluginizer->create('Magento_Test_Di_Child');
        $this->assertEquals('_A__B_|BAtestStringAB|_B__A_', $child->wrap('testString'));
    }

    public function testPluginsAreOrdered()
    {
        $this->_pluginizer->configure(
            array('Magento_Test_Di_Child' => array(
                'interceptors' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B', 'before' => '-')
                )
            ))
        );

        $child = $this->_pluginizer->create('Magento_Test_Di_Child');
        $this->assertEquals('_B__A_(ABtestStringBA)_A__B_', $child->wrap('testString'));
    }

    public function testPluginsAreAddedToInstances()
    {
        $this->_pluginizer->configure(
            array('customChild' => array(
                'type' => 'Magento_Test_Di_Child',
                'interceptors' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                ),
                'parameters' => array(
                    'wrapperSymbol' => '/'
                )
            ))
        );

        $child = $this->_pluginizer->create('customChild');
        $this->assertEquals('_A__B_/BAtestStringAB/_B__A_', $child->wrap('testString'));
    }

    public function testInstanceIsUsedAsPlugin()
    {
        $this->_pluginizer->configure(
            array(
                'Magento_Test_Di_Child' => array(
                    'interceptors' => array(
                        'first' => array('instance' => 'customAInterceptor'),
                        'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                    )
                ),
                'customAInterceptor' => array(
                    'type' => 'Magento_Test_Di_Child_Interceptor_A',
                    'parameters' => array(
                        'wrapperSym' => 'AAA'
                    )
                )
            )
        );

        $child = $this->_pluginizer->create('Magento_Test_Di_Child');
        $this->assertEquals('_AAA__B_|BAAAtestStringAAAB|_B__AAA_', $child->wrap('testString'));
    }
}
