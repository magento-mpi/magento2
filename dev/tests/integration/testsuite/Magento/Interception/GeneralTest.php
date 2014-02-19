<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception;

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
        $relations = new \Magento\ObjectManager\Relations\Runtime($classReader);
        $definitions = new \Magento\ObjectManager\Definition\Runtime($classReader);
        $config = new \Magento\Interception\ObjectManager\Config($relations, $definitions);
        $factory = new \Magento\ObjectManager\Factory\Factory($config, null, $definitions);

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
        $this->_objectManager = new \Magento\ObjectManager\ObjectManager(
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
            'Magento\Interception\PluginList' => 'Magento\Interception\PluginList\PluginList',
            'Magento\Interception\Chain' => 'Magento\Interception\Chain\Chain'
        )));
    }

    public function testMethodCanBePluginized()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<P:D>1: <D>test</D></P:D>', $subject->D('test'));
    }

    public function testPluginCanCallOnlyNextMethodOnNext()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals(
            '<IP:aG><P:aG><G><P:G><P:bG><IP:G><IP:bG>test</IP:bG></IP:G></P:bG></P:G></G></P:aG></IP:aG>',
            $subject->G('test')
        );
    }

    public function testBeforeAndAfterPluginsAreExecuted()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals(
            '<IP:F><P:D>1: <D>prefix_<F><IP:C><P:C><C>test</C></P:C>'
            . '</IP:C></F></D></P:D></IP:F>',
            $subject->A('prefix_')->F('test')
        );
    }

    public function testPluginCallsOtherMethodsOnSubject()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals(
            '<P:K><IP:F><P:D>1: <D>prefix_<F><IP:C><P:C><C><IP:C><P:C><C>test'
                . '</C></P:C></IP:C></C></P:C></IP:C></F></D></P:D></IP:F></P:K>',
            $subject->A('prefix_')->K('test')
        );
    }

    public function testInterfacePluginsAreInherited()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<IP:C><P:C><C>test</C></P:C></IP:C>', $subject->C('test'));
    }

    public function testInternalMethodCallsAreIntercepted()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<B>12<IP:C><P:C><C>1</C></P:C></IP:C></B>', $subject->B('1', '2'));
    }

    public function testChainedMethodsAreIntercepted()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<P:D>1: <D>prefix_test</D></P:D>', $subject->A('prefix_')->D('test'));
    }

    public function testFinalMethodWorks()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<P:D>1: <D>prefix_test</D></P:D>', $subject->A('prefix_')->D('test'));
        $this->assertEquals('<E>prefix_final</E>', $subject->E('final'));
        $this->assertEquals('<P:D>2: <D>prefix_test</D></P:D>', $subject->D('test'));
    }

    public function testObjectKeepsStateBetweenInvocations()
    {
        $subject = $this->_objectManager->create('Magento\Interception\Fixture\Intercepted');
        $this->assertEquals('<P:D>1: <D>test</D></P:D>', $subject->D('test'));
        $this->assertEquals('<P:D>2: <D>test</D></P:D>', $subject->D('test'));
    }
}
