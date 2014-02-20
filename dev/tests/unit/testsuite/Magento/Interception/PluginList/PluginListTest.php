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

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-21212');
        $readerMap = include(__DIR__ . '/../_files/reader_mock_map.php');
        $readerMock = $this->getMock('\Magento\ObjectManager\Config\Reader\Dom', array(), array(), '', false);
        $readerMock->expects($this->any())
            ->method('read')
            ->will($this->returnValueMap($readerMap));

        $this->_configScopeMock = $this->getMock('\Magento\Config\ScopeInterface');
        $cacheMock = $this->getMock('Magento\Config\CacheInterface');
        // turn cache off
        $cacheMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $omConfigMock = $this->getMock('Magento\Interception\ObjectManager\Config');
        $omConfigMock->expects($this->any())
            ->method('getOriginalInstanceType')
            ->will($this->returnArgument(0));

        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');

        $this->_model = new \Magento\Interception\PluginList\PluginList(
            $readerMock,
            $this->_configScopeMock,
            $cacheMock,
            new \Magento\ObjectManager\Relations\Runtime(),
            $omConfigMock,
            new \Magento\Interception\Definition\Runtime(),
            $this->_objectManagerMock,
            array('global'),
            'interception',
            null
        );
    }

    /**
     * @param array $expectedResult
     * @param string $type
     * @param string $method
     * @param string $scenario
     * @param string $scopeCode
     * @dataProvider getPluginsDataProvider
     */
    public function testGetPlugins(array $expectedResult, $type, $method, $scenario, $scopeCode)
    {
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue($scopeCode));
        $this->assertEquals(
            $expectedResult,
            $this->_model->getPlugins($type, $method, $scenario)
        );
    }

    /**
     * @return array
     */
    public function getPluginsDataProvider()
    {
        return array(
            array(
                array('Magento\Interception\Custom\Module\Model\ItemPlugin\Simple'),
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'after',
                'global',
            ),
            array(
                // advanced plugin has lower sort order
                array('Magento\Interception\Custom\Module\Model\ItemPlugin\Advanced',
                      'Magento\Interception\Custom\Module\Model\ItemPlugin\Simple'),
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'after',
                'backend',
            ),
            array(
                array('Magento\Interception\Custom\Module\Model\ItemPlugin\Advanced'),
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'around',
                'backend',
            ),
            array(
                // simple plugin is disabled in configuration for
                // \Magento\Interception\Custom\Module\Model\Item in frontend
                array(),
                'Magento\Interception\Custom\Module\Model\Item',
                'getName',
                'after',
                'frontend',
            ),
            // test plugin inheritance
            array(
                array('Magento\Interception\Custom\Module\Model\ItemPlugin\Simple'),
                'Magento\Interception\Custom\Module\Model\Item\Enhanced',
                'getName',
                'after',
                'global',
            ),
            array(
                // simple plugin is disabled in configuration for parent
                array('Magento\Interception\Custom\Module\Model\ItemPlugin\Advanced'),
                'Magento\Interception\Custom\Module\Model\Item\Enhanced',
                'getName',
                'after',
                'frontend',
            ),
            array(
                array('Magento\Interception\Custom\Module\Model\ItemPlugin\Advanced'),
                'Magento\Interception\Custom\Module\Model\Item\Enhanced',
                'getName',
                'around',
                'frontend',
            ),
            array(
                array(),
                'Magento\Interception\Custom\Module\Model\ItemContainer',
                'getName',
                'after',
                'global',
            ),
            array(
                array('Magento\Interception\Custom\Module\Model\ItemContainerPlugin\Simple'),
                'Magento\Interception\Custom\Module\Model\ItemContainer',
                'getName',
                'after',
                'backend',
            ),
        );
    }
}
