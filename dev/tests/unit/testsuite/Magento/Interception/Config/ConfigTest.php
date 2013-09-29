<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Interception\Config;

require_once __DIR__ . '/../Custom/Module/Model/Item.php';
require_once __DIR__ . '/../Custom/Module/Model/Item/Enhanced.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainer.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainer/Enhanced.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemContainerPlugin/Simple.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemPlugin/Simple.php';
require_once __DIR__ . '/../Custom/Module/Model/ItemPlugin/Advanced.php';

class ConfigTest extends \PHPUnit_Framework_TestCase
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
        $fixtureBasePath = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/..');
        $fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('di.xml', 'global', array($fixtureBasePath . '/Custom/Module/etc/di.xml')),
                array('di.xml', 'backend', array($fixtureBasePath . '/Custom/Module/etc/backend/di.xml')),
                array('di.xml', 'frontend', array($fixtureBasePath . '/Custom/Module/etc/frontend/di.xml')),
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
        $this->_configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_configScopeMock->expects($this->any())
            ->method('getAllScopes')
            ->will($this->returnValue(array('global', 'backend', 'frontend')));
        $cacheMock = $this->getMock('Magento\Cache\FrontendInterface');
        // turn cache off
        $cacheMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        $omConfigMock = $this->getMock('Magento\ObjectManager\Config');
        $omConfigMock->expects($this->any())
            ->method('getInstanceType')
            ->will($this->returnArgument(0));
        $this->_model = new \Magento\Interception\Config\Config(
            $reader,
            $this->_configScopeMock,
            $cacheMock,
            new \Magento\ObjectManager\Relations\Runtime(),
            $omConfigMock,
            null,
            null,
            'interception'
        );
    }

    /**
     * @param boolean $expectedResult
     * @param string $type
     * @dataProvider hasPluginsDataProvider
     */
    public function testHasPlugins($expectedResult, $type)
    {
        $this->assertEquals($expectedResult, $this->_model->hasPlugins($type));
    }

    public function hasPluginsDataProvider()
    {
        return array(
            // item container has plugins only in the backend scope
            array(
                true,
                'Magento\Interception\Custom\Module\Model\ItemContainer',
            ),
            array(
                true,
                'Magento\Interception\Custom\Module\Model\Item',
            ),
            array(
                true,
                'Magento\Interception\Custom\Module\Model\Item\Enhanced',
            ),
            array(
                // the following model has only inherited plugins
                true,
                'Magento\Interception\Custom\Module\Model\ItemContainer\Enhanced',
            )
        );
    }
}
