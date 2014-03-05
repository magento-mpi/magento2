<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml observer
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
namespace Magento\CatalogPermissions\Model\Adminhtml;

use Magento\App\CacheInterface;
use Magento\AuthorizationInterface;
use Magento\Catalog\Block\Adminhtml\Category\Tabs;
use Magento\Catalog\Model\Category;
use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Event\Observer as EventObserver;

class Observer
{
    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var CacheInterface
     */
    protected $coreCache;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $appConfig;

    /**
     * @param CacheInterface $coreCache
     * @param AuthorizationInterface $authorization
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param ConfigInterface $appConfig
     */
    public function __construct(
        CacheInterface $coreCache,
        AuthorizationInterface $authorization,
        ConfigInterface $appConfig,
        \Magento\Indexer\Model\IndexerInterface $indexer
    ) {
        $this->indexer = $indexer;
        $this->appConfig = $appConfig;
        $this->coreCache = $coreCache;
        $this->authorization = $authorization;
    }

    /**
     * Refresh category related cache on catalog permissions config save
     *
     * @return $this
     */
    public function cleanCacheOnConfigChange()
    {
        $this->coreCache->clean(array(Category::CACHE_TAG));
        if ($this->appConfig->isEnabled()) {
            $this->getIndexer()->invalidate();
        }
        return $this;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function addCategoryPermissionTab(EventObserver $observer)
    {
        if (!$this->appConfig->isEnabled()) {
            return $this;
        }
        if (!$this->authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Tabs */

        $tabs->addTab(
            'permissions',
            'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions'
        );

        return $this;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        }
        return $this->indexer;
    }
}
