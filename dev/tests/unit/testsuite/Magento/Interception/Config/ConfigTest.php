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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $omConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $definitionMock;

    protected function setUp()
    {
        $this->readerMock = $this->getMock('\Magento\ObjectManager\Config\Reader\Dom', array(), array(), '', false);
        $this->configScopeMock = $this->getMock('Magento\Framework\Config\ScopeListInterface');
        $this->cacheMock = $this->getMock('Magento\Cache\FrontendInterface');
        $this->omConfigMock = $this->getMock('Magento\Interception\ObjectManager\Config');
        $this->definitionMock = $this->getMock('Magento\ObjectManager\Definition');
    }

    /**
     * @param boolean $expectedResult
     * @param string $type
     * @dataProvider hasPluginsDataProvider
     */
    public function testHasPluginsWhenDataIsNotCached($expectedResult, $type)
    {
        $readerMap = include(__DIR__ . '/../_files/reader_mock_map.php');
        $this->readerMock->expects($this->any())
            ->method('read')
            ->will($this->returnValueMap($readerMap));
        $this->configScopeMock->expects($this->any())
            ->method('getAllScopes')
            ->will($this->returnValue(array('global', 'backend', 'frontend')));
        // turn cache off
        $this->cacheMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));
        $this->omConfigMock->expects($this->any())
            ->method('getOriginalInstanceType')
            ->will($this->returnValueMap(
                array(
                    array(
                        'Magento\Interception\Custom\Module\Model\ItemContainer',
                        'Magento\Interception\Custom\Module\Model\ItemContainer',
                    ),
                    array(
                        'Magento\Interception\Custom\Module\Model\Item',
                        'Magento\Interception\Custom\Module\Model\Item',
                    ),
                    array(
                        'Magento\Interception\Custom\Module\Model\Item\Enhanced',
                        'Magento\Interception\Custom\Module\Model\Item\Enhanced',
                    ),
                    array(
                        'Magento\Interception\Custom\Module\Model\ItemContainer\Enhanced',
                        'Magento\Interception\Custom\Module\Model\ItemContainer\Enhanced',
                    ),
                    array(
                        'Magento\Interception\Custom\Module\Model\ItemProxy',
                        'Magento\Interception\Custom\Module\Model\ItemProxy',
                    ),
                    array(
                        'virtual_custom_item',
                        'Magento\Interception\Custom\Module\Model\Item',
                    ),
                )
            ));
        $this->definitionMock->expects($this->any())->method('getClasses')->will($this->returnValue(
            array(
                'Magento\Interception\Custom\Module\Model\ItemProxy',
            )
        ));
        $model = new \Magento\Interception\Config\Config(
            $this->readerMock,
            $this->configScopeMock,
            $this->cacheMock,
            new \Magento\ObjectManager\Relations\Runtime(),
            $this->omConfigMock,
            $this->definitionMock,
            'interception'
        );

        $this->assertEquals($expectedResult, $model->hasPlugins($type));
    }

    /**
     * @param boolean $expectedResult
     * @param string $type
     * @dataProvider hasPluginsDataProvider
     */
    public function testHasPluginsWhenDataIsCached($expectedResult, $type)
    {
        $cacheId = 'interception';
        $interceptionData = array(
            'Magento\Interception\Custom\Module\Model\ItemContainer' => true,
            'Magento\Interception\Custom\Module\Model\Item' => true,
            'Magento\Interception\Custom\Module\Model\Item\Enhanced' => true,
            'Magento\Interception\Custom\Module\Model\ItemContainer\Enhanced' => true,
            'Magento\Interception\Custom\Module\Model\ItemProxy' => false,
            'virtual_custom_item' => true,
        );
        $this->readerMock->expects($this->never())->method('read');
        $this->cacheMock->expects($this->never())->method('save');
        $this->cacheMock->expects($this->any())
            ->method('load')
            ->with($cacheId)
            ->will($this->returnValue(serialize($interceptionData)));
        $model = new \Magento\Interception\Config\Config(
            $this->readerMock,
            $this->configScopeMock,
            $this->cacheMock,
            new \Magento\ObjectManager\Relations\Runtime(),
            $this->omConfigMock,
            $this->definitionMock,
            $cacheId
        );

        $this->assertEquals($expectedResult, $model->hasPlugins($type));
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
            ),
            array(
                false,
                'Magento\Interception\Custom\Module\Model\ItemProxy',
            ),
            array(
                true,
                'virtual_custom_item',
            )
        );
    }
}
