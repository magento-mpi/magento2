<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception;

use Magento\ObjectManager;

class GeneralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configReader;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $classReader = new \Magento\Code\Reader\ClassReader();
        $relations = new ObjectManager\Relations\Runtime($classReader);
        $definitions = new ObjectManager\Definition\Runtime($classReader);
        $config = new \Magento\Interception\ObjectManager\Config($relations, $definitions);
        $factory = new ObjectManager\Factory\Factory($config, null, $definitions);

        $this->_configReader = $this->getMock('Magento\Config\ReaderInterface');
        $this->_configReader->expects($this->any())->method('read')->will($this->returnValue(array(
            'Magento\Interception\Fixture\InterceptedInterface' => array('plugins' => array(
                'first' => array(
                    'instance' => 'Magento\Interception\Fixture\Intercepted\InterfacePlugin',
                    'sortOrder' => 10
                )
            )),
            'Magento\Interception\Fixture\Intercepted' => array('plugins' => array(
                'second' => array(
                    'instance' => 'Magento\Interception\Fixture\Intercepted\Plugin',
                    'sortOrder' => 20
                )
            ))
        )));

        $areaList = $this->getMock('Magento\App\AreaList', array(), array(), '', false);
        $areaList->expects($this->any())->method('getCodes')->will($this->returnValue(array()));
        $configScope = new \Magento\Config\Scope($areaList, 'global');
        $cache = $this->getMock('Magento\Config\CacheInterface');
        $cache->expects($this->any())->method('load')->will($this->returnValue(false));
        $interceptionConfig = new Config\Config(
            $this->_configReader, $configScope, $cache, $relations, $config
        );
        $interceptionDefinitions = new Definition\Runtime();
        $this->_objectManager = new ObjectManager\ObjectManager(
            $factory, $config, array(
                'Magento\Config\CacheInterface' => $cache,
                'Magento\Config\ScopeInterface' => $configScope,
                'Magento\Config\ReaderInterface' => $this->_configReader,
                'Magento\ObjectManager\Relations' => $relations,
                'Magento\ObjectManager\Config' => $config,
                'Magento\Interception\Definition' => $interceptionDefinitions
            )
        );
        $config->setInterceptionConfig($interceptionConfig);
        $config->extend(array('preferences' => array(
            'Magento\Interception\PluginList' => 'Magento\Interception\PluginList\PluginList'
        )));
    }

    public function testMethodCanBePluginized()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<I_P_D>1: <I_D>test</I_D></I_P_D>', $subject->D('test'));
    }

    public function testPluginCanCallOnlyNextMethodOnNext()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals(
            '<II_P_F><I_P_D>1: <I_D>prefix_<I_F><II_P_C><I_P_C><I_C>test</I_C></I_P_C>'
                . '</II_P_C></I_F></I_D></I_P_D></II_P_F>',
            $subject->A('prefix_')->F('test')
        );
    }

    public function testPluginCallsOtherMethodsOnSubject()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals(
            '<I_P_K><II_P_F><I_P_D>1: <I_D>prefix_<I_F><II_P_C><I_P_C><I_C><II_P_C><I_P_C><I_C>test'
                . '</I_C></I_P_C></II_P_C></I_C></I_P_C></II_P_C></I_F></I_D></I_P_D></II_P_F></I_P_K>',
            $subject->A('prefix_')->K('test')
        );
    }

    public function testInterfacePluginsAreInherited()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<II_P_C><I_P_C><I_C>test</I_C></I_P_C></II_P_C>', $subject->C('test'));
    }

    public function testInternalMethodCallsAreIntercepted()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<I_B>12<II_P_C><I_P_C><I_C>1</I_C></I_P_C></II_P_C></I_B>', $subject->B('1', '2'));
    }

    public function testChainedMethodsAreIntercepted()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<I_P_D>1: <I_D>prefix_test</I_D></I_P_D>', $subject->A('prefix_')->D('test'));
    }

    public function testFinalMethodWorks()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<I_P_D>1: <I_D>prefix_test</I_D></I_P_D>', $subject->A('prefix_')->D('test'));
        $this->assertEquals('<I_E>prefix_final</I_E>', $subject->E('final'));
        $this->assertEquals('<I_P_D>2: <I_D>prefix_test</I_D></I_P_D>', $subject->D('test'));
    }

    public function testSerializationWorksProperly()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $data = serialize($subject);
        $subject = unserialize($data);
        $this->assertEquals('<I_P_D>1: <I_D>test</I_D></I_P_D>', $subject->D('test'));
        $this->assertEquals('<II_P_C><I_P_C><I_C>test</I_C></I_P_C></II_P_C>', $subject->C('test'));
        $this->assertEquals('<I_B>12<II_P_C><I_P_C><I_C>1</I_C></I_P_C></II_P_C></I_B>', $subject->B('1', '2'));
        $this->assertEquals('<I_P_D>2: <I_D>prefix_test</I_D></I_P_D>', $subject->A('prefix_')->D('test'));
    }

    public function testObjectKeepsStateBetweenInvocations()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<I_P_D>1: <I_D>test</I_D></I_P_D>', $subject->D('test'));
        $this->assertEquals('<I_P_D>2: <I_D>test</I_D></I_P_D>', $subject->D('test'));
    }
}
