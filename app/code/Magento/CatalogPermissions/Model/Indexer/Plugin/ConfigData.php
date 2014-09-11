<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

use Magento\Framework\App\CacheInterface;
use Magento\CatalogPermissions\App\ConfigInterface;

class ConfigData
{
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
     * @var \Magento\Backend\Model\Config\Loader
     */
    protected $configLoader;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param CacheInterface $coreCache
     * @param ConfigInterface $appConfig
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Backend\Model\Config\Loader $configLoader
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        CacheInterface $coreCache,
        ConfigInterface $appConfig,
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Backend\Model\Config\Loader $configLoader,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->indexer = $indexer;
        $this->appConfig = $appConfig;
        $this->coreCache = $coreCache;
        $this->configLoader = $configLoader;
        $this->storeManager = $storeManager;
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

    /**
     * Return formatted config data for current section
     *
     * @param \Magento\Backend\Model\Config $config
     * @return array
     */
    protected function getConfig(\Magento\Backend\Model\Config $config)
    {
        $scope = 'default';
        $scopeId = 0;
        if ($config->getStore()) {
            $scope = 'stores';
            $store = $this->storeManager->getStore($config->getStore());
            $scopeId = (int)$store->getId();
        } elseif ($config->getWebsite()) {
            $scope = 'websites';
            $website = $this->storeManager->getWebsite($config->getWebsite());
            $scopeId = (int)$website->getId();
        }
        return $this->configLoader->getConfigByPath(
            $config->getSection() . '/magento_catalogpermissions',
            $scope,
            $scopeId,
            false
        );
    }

    /**
     *  Invalidation indexer after configuration of permission was changed
     *
     * @param \Magento\Backend\Model\Config $subject
     * @param \Closure $proceed
     *
     * @return \Magento\Backend\Model\Config
     */
    public function aroundSave(\Magento\Backend\Model\Config $subject, \Closure $proceed)
    {
        $oldConfig = $this->getConfig($subject, false);
        $result = $proceed();
        $newConfig = $this->getConfig($subject, false);
        if ($this->checkForValidating($oldConfig, $newConfig) && $this->appConfig->isEnabled()) {
            $this->coreCache->clean(array(\Magento\Catalog\Model\Category::CACHE_TAG));
            $this->getIndexer()->invalidate();
        }

        return $result;
    }

    /**
     * @param array $oldConfig
     * @param array $newConfig
     * @return bool
     */
    protected function checkForValidating(array $oldConfig, array $newConfig)
    {
        $needInvalidating = false;
        foreach ($oldConfig as $key => $value) {
            if ($newConfig[$key] != $value) {
                $needInvalidating = true;
                break;
            }
        }
        return $needInvalidating;
    }
}
