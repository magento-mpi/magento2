<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class CacheTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushAllAction()
    {
        /** @var $cache \Magento\Framework\App\Cache */
        $cache = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Framework\App\Cache');
        $this->assertNotEmpty($cache->load('APPLICATION_FIXTURE'));

        $this->dispatch('backend/admin/cache/flushAll');

        /** @var $cachePool \Magento\Framework\App\Cache\Frontend\Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Cache\Frontend\Pool'
        );
        /** @var $cacheFrontend \Magento\Framework\Cache\FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertFalse($cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE'));
        }
    }

    /**
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushSystemAction()
    {
        $this->dispatch('backend/admin/cache/flushSystem');

        /** @var $cache \Magento\Framework\App\Cache */
        $cache = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Framework\App\Cache');
        /** @var $cachePool \Magento\Framework\App\Cache\Frontend\Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Cache\Frontend\Pool'
        );
        /** @var $cacheFrontend \Magento\Framework\Cache\FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertSame(
                'non-application cache data',
                $cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE')
            );
        }
    }

    /**
     * @dataProvider massActionsInvalidTypesDataProvider
     * @param $action
     */
    public function testMassActionsInvalidTypes($action)
    {
        $this->getRequest()->setParams(array('types' => array('invalid_type_1', 'invalid_type_2', 'config')));
        $this->dispatch('backend/admin/cache/' . $action);
        $this->assertSessionMessages(
            $this->contains("Specified cache type(s) don't exist: invalid_type_1, invalid_type_2"),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @return array
     */
    public function massActionsInvalidTypesDataProvider()
    {
        return array(
            'enable' => array('massEnable'),
            'disable' => array('massDisable'),
            'refresh' => array('massRefresh')
        );
    }
}
