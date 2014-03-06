<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Interception\PluginList;

require_once __DIR__ . '/../Custom/Module/Model/Item.php';
require_once __DIR__ . '/../Custom/Module/Model/Item/Enhanced.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainer.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainer/Enhanced.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainerPlugin/Simple.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemPlugin/Simple.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemPlugin/Advanced.php';

class PluginListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Interception\PluginList\PluginList
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $readerMap = include(__DIR__ . '/../_files/reader_mock_map.php');
        $readerMock = $this->getMock('\Magento\ObjectManager\Config\Reader\Dom', array(), array(), '', false);
        $readerMock->expects($this->any())
            ->method('read')
            ->will($this->returnValueMap($readerMap));

        $this->_configScopeMock = $this->getMock('\Magento\Config\ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
        // turn cache off
        $this->_cacheMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $omConfigMock = $this->getMock('Magento\Interception\ObjectManager\Config');
        $omConfigMock->expects($this->any())
            ->method('getOriginalInstanceType')
            ->will($this->returnArgument(0));

        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnArgument(0));

        $definitions = new \Magento\ObjectManager\Definition\Runtime();

        $this->_model = new \Magento\Interception\PluginList\PluginList(
            $readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            new \Magento\ObjectManager\Relations\Runtime(),
            $omConfigMock,
            new \Magento\Interception\Definition\Runtime(),
            $this->_objectManagerMock,
            $definitions,
            array('global'),
            'interception'
        );
    }

    public function testGetPlugin()
    {
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue('backend'));
        $this->_model->getNext('Magento\Interception\Custom\Module\Model\Item', 'getName');
        $this->_model->getNext('Magento\Interception\Custom\Module\Model\ItemContainer', 'getName');

        $this->assertEquals(
            'Magento\Interception\Custom\Module\Model\ItemPlugin\Simple',
            $this->_model->getPlugin('Magento\Interception\Custom\Module\Model\Item', 'simple_plugin')
        );
        $this->assertEquals(
            'Magento\Interception\Custom\Module\Model\ItemPlugin\Advanced',
            $this->_model->getPlugin('Magento\Interception\Custom\Module\Model\Item', 'advanced_plugin')
        );
        $this->assertEquals(
            'Magento\Interception\Custom\Module\Model\ItemContainerPlugin\Simple',
            $this->_model->getPlugin('Magento\Interception\Custom\Module\Model\ItemContainer', 'simple_plugin')
        );
    }

    /**
     * @param $expectedResult
     * @param $type
     * @param $method
     * @param $scopeCode
     * @param string $code
     * @dataProvider getPluginsDataProvider
     */
    public function testGetPlugins($expectedResult, $type, $method, $scopeCode, $code = '__self')
    {
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue($scopeCode));
        $this->assertEquals(
            $expectedResult,
            $this->_model->getNext($type, $method, $code)
        );
    }

    /**
     * @return array
     */
    public function getPluginsDataProvider()
    {
        return array(
            array(
                array(4 => array('simple_plugin')),
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'global',
            ),
            array(
                // advanced plugin has lower sort order
                array(2 => 'advanced_plugin', 4 => array('advanced_plugin')),
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'backend',
            ),
            array(
                // advanced plugin has lower sort order
                array(4 => array('simple_plugin')),
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'backend',
                'advanced_plugin'
            ),
            array(
                // simple plugin is disabled in configuration for
                // \Magento\Interception\Custom\Module\Model\Item in frontend
                null,
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'frontend',
            ),
            // test plugin inheritance
            array(
                array(4 => array('simple_plugin')),
                'Magento\Interception\Custom\Module\Model\Item\Enhanced',
                'getName',
                'global',
            ),
            array(
                // simple plugin is disabled in configuration for parent
                array(2 => 'advanced_plugin', 4 => array('advanced_plugin')),
                'Magento\Interception\Custom\Module\Model\Item\Enhanced',
                'getName',
                'frontend',
            ),
            array(
                null,
                'Magento\Interception\Custom\Module\Model\ItemContainer',
                'getName',
                'global',
            ),
            array(
                array(4 => array('simple_plugin')),
                'Magento\Interception\Custom\Module\Model\ItemContainer',
                'getName',
                'backend',
            ),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers \Magento\Interception\PluginList\PluginList::getNext
     * @covers \Magento\Interception\PluginList\PluginList::_inheritPlugins
     */
    public function testInheritPluginsWithNonExistingClass()
    {
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue('frontend'));

        $this->_model->getNext('SomeType', 'someMethod');
    }

    /**
     * @covers \Magento\Interception\PluginList\PluginList::getNext
     * @covers \Magento\Interception\PluginList\PluginList::_loadScopedData
     */
    public function testLoadScopedDataCached()
    {
        $this->_configScopeMock->expects($this->once())
            ->method('getCurrentScope')
            ->will($this->returnValue('scope'));

        $data = array(array('key'), array('key'), array('key'));

        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with('global|scope|interception')
            ->will($this->returnValue(serialize($data)));

        $this->assertEquals(null, $this->_model->getNext('Type', 'method'));
    }
}
