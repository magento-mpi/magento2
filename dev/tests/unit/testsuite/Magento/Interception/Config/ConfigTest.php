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
    protected $configScopeMock;

    protected function setUp()
    {
        $readerMap = include(__DIR__ . '/../_files/reader_mock_map.php');
        $readerMock = $this->getMock('\Magento\ObjectManager\Config\Reader\Dom', array(), array(), '', false);
        $readerMock->expects($this->any())
            ->method('read')
            ->will($this->returnValueMap($readerMap));

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
        $this->configScopeMock = $this->getMock('Magento\Config\ScopeListInterface');
        $this->configScopeMock->expects($this->any())
            ->method('getAllScopes')
            ->will($this->returnValue(array('global', 'backend', 'frontend')));
        $cacheMock = $this->getMock('Magento\Cache\FrontendInterface');
        // turn cache off
        $cacheMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        $omConfigMock = $this->getMock('Magento\Interception\ObjectManager\Config');
        $omConfigMock->expects($this->any())
            ->method('getOriginalInstanceType')
            ->will($this->returnArgument(0));
        $this->model = new \Magento\Interception\Config\Config(
            $reader,
            $this->configScopeMock,
            $cacheMock,
            new \Magento\ObjectManager\Relations\Runtime(),
            $omConfigMock,
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
        $this->assertEquals($expectedResult, $this->model->hasPlugins($type));
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
