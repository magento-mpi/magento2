<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class ConfigDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreCacheMock;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appConfigMock;

    /**
     * @var \Magento\Backend\Model\Config\Loader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoaderMock;

    /**
     * @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Closure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $closureMock;

    /**
     * @var \Magento\Backend\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendConfigMock;

    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Plugin\ConfigData
     */
    protected $configData;

    protected function setUp()
    {
        $this->coreCacheMock = $this->getMock('Magento\Framework\App\Cache', array('clean'), array(), '', false);
        $this->appConfigMock = $this->getMock(
            'Magento\CatalogPermissions\App\Backend\Config',
            array('isEnabled'),
            array(),
            '',
            false
        );
        $this->indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('getId', 'invalidate'),
            array(),
            '',
            false
        );
        $this->configLoaderMock = $this->getMock(
            'Magento\Backend\Model\Config\Loader',
            array('getConfigByPath'),
            array(),
            '',
            false
        );
        $this->storeManagerMock = $this->getMock(
            'Magento\Store\Model\StoreManager',
            array('getStore', 'getWebsite'),
            array(),
            '',
            false
        );
        $backendConfigMock = $this->backendConfigMock = $this->getMock(
            'Magento\Backend\Model\Config',
            array('getStore', 'getWebsite', 'getSection'),
            array(),
            '',
            false
        );
        $this->closureMock = function () use ($backendConfigMock) {
            return $backendConfigMock;
        };

        $this->configData = new ConfigData(
            $this->coreCacheMock,
            $this->appConfigMock,
            $this->indexerMock,
            $this->configLoaderMock,
            $this->storeManagerMock
        );
    }

    public function testAroundSaveWithoutChanges()
    {
        $section = 'test';
        $this->backendConfigMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue(false));
        $this->backendConfigMock->expects($this->exactly(2))->method('getWebsite')->will($this->returnValue(false));
        $this->backendConfigMock->expects($this->exactly(2))->method('getSection')->will($this->returnValue($section));
        $this->configLoaderMock->expects(
            $this->exactly(2)
        )->method(
            'getConfigByPath'
        )->with(
            $section . '/magento_catalogpermissions',
            'default',
            0,
            false
        )->will(
            $this->returnValue(array('test' => 1))
        );
        $this->appConfigMock->expects($this->never())->method('isEnabled');

        $this->configData->aroundSave($this->backendConfigMock, $this->closureMock);
    }

    public function testAroundSaveIndexerTurnedOff()
    {
        $section = 'test';
        $storeId = 5;

        $store = $this->getStore();
        $store->expects($this->exactly(2))->method('getId')->will($this->returnValue($storeId));
        $this->backendConfigMock->expects($this->exactly(4))->method('getStore')->will($this->returnValue($store));
        $this->storeManagerMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue($store));

        $this->backendConfigMock->expects($this->never())->method('getWebsite');

        $this->backendConfigMock->expects($this->exactly(2))->method('getSection')->will($this->returnValue($section));
        $this->prepareConfigLoader($section, $storeId, 'stores');

        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->coreCacheMock->expects($this->never())->method('clean');

        $this->configData->aroundSave($this->backendConfigMock, $this->closureMock);
    }

    public function testAroundSaveIndexerTurnedOn()
    {
        $section = 'test';
        $websiteId = 20;

        $store = $this->getStore();
        $store->expects($this->exactly(2))->method('getId')->will($this->returnValue($websiteId));
        $this->backendConfigMock->expects($this->exactly(4))->method('getWebsite')->will($this->returnValue($store));
        $this->storeManagerMock->expects($this->exactly(2))->method('getWebsite')->will($this->returnValue($store));

        $this->storeManagerMock->expects($this->never())->method('getStore');

        $this->backendConfigMock->expects($this->exactly(2))->method('getStore');

        $this->backendConfigMock->expects($this->exactly(2))->method('getSection')->will($this->returnValue($section));

        $this->prepareConfigLoader($section, $websiteId, 'websites');

        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $this->coreCacheMock->expects(
            $this->once()
        )->method(
            'clean'
        )->with(
            array(\Magento\Catalog\Model\Category::CACHE_TAG)
        );

        $this->indexerMock->expects($this->once())->method('getId')->will($this->returnValue(10));

        $this->indexerMock->expects($this->once())->method('invalidate');

        $this->configData->aroundSave($this->backendConfigMock, $this->closureMock);
    }

    /**
     * @return \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getStore()
    {
        $store = $this->getMock('Magento\Store\Model\Store', array('getId', '__wakeup'), array(), '', false);
        return $store;
    }

    /**
     * @return \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getWebsite()
    {
        $website = $this->getMock('Magento\Store\Model\Website', array('getId', '__wakeup'), array(), '', false);
        return $website;
    }

    /**
     * @param string $section
     * @param int $objectId
     * @param string $type
     */
    protected function prepareConfigLoader($section, $objectId, $type)
    {
        $counter = 0;
        $this->configLoaderMock->expects(
            $this->exactly(2)
        )->method(
            'getConfigByPath'
        )->with(
            $section . '/magento_catalogpermissions',
            $type,
            $objectId,
            false
        )->will(
            $this->returnCallback(
                function () use (&$counter) {
                    return ++$counter % 2 ? array('test' => 1) : array('test' => 2);
                }
            )
        );
    }
}
