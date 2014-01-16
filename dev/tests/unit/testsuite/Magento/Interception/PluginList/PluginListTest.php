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
     * @var \Magento\Interception\Config\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    protected function setUp()
    {
        $fixtureBasePath        = __DIR__ . '/..';
        $moduleEtcPath          = $fixtureBasePath . '/Custom/Module/etc/di.xml';
        $moduleBackendEtcPath   = $fixtureBasePath . '/Custom/Module/etc/backend/di.xml';
        $moduleFrontendEtcPath  = $fixtureBasePath . '/Custom/Module/etc/frontend/di.xml';

        $fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('di.xml', 'global', array($moduleEtcPath => file_get_contents($moduleEtcPath))),
                array('di.xml', 'backend', array($moduleBackendEtcPath => file_get_contents($moduleBackendEtcPath))),
                array('di.xml', 'frontend', array($moduleFrontendEtcPath => file_get_contents($moduleFrontendEtcPath))),
            )));

        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));

        $reader = new \Magento\ObjectManager\Config\Reader\Dom(
            $fileResolverMock,
            new \Magento\ObjectManager\Config\Mapper\Dom(),
            new \Magento\ObjectManager\Config\SchemaLocator(),
            $validationStateMock
        );
        $this->_configScopeMock = $this->getMock('\Magento\Config\ScopeInterface');
        $cacheMock = $this->getMock('Magento\Config\CacheInterface');
        // turn cache off
        $cacheMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $omConfigMock = $this->getMock('Magento\ObjectManager\Config');
        $omConfigMock->expects($this->any())
            ->method('getInstanceType')
            ->will($this->returnArgument(0));
        $this->_model = new \Magento\Interception\PluginList\PluginList(
            $reader,
            $this->_configScopeMock,
            $cacheMock,
            new \Magento\ObjectManager\Relations\Runtime(),
            $omConfigMock,
            new \Magento\Interception\Definition\Runtime(),
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
