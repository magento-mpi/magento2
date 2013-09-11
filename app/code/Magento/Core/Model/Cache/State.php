<?php
/**
 * An ultimate accessor to cache types' statuses
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Cache;

class State implements \Magento\Core\Model\Cache\StateInterface
{
    /**
     * Cache identifier used to store cache type statuses
     */
    const CACHE_ID  = 'core_cache_options';

    /**
     * Persistent storage of cache type statuses
     *
     * @var \Magento\Core\Model\Resource\Cache
     */
    private $_resource;

    /**
     * Cache frontend to delegate actual cache operations to
     *
     * @var \Magento\Cache\FrontendInterface
     */
    private $_cacheFrontend;

    /**
     * Associative array of cache type codes and their statuses (enabled/disabled)
     *
     * @var array
     */
    private $_typeStatuses = array();

    /**
     * @param \Magento\Core\Model\Resource\Cache $resource
     * @param \Magento\Core\Model\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Core\Model\App\State $appState
     * @param bool $banAll Whether all cache types are forced to be disabled
     */
    public function __construct(
        \Magento\Core\Model\Resource\Cache $resource,
        \Magento\Core\Model\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Core\Model\App\State $appState,
        $banAll = false
    ) {
        $this->_resource = $resource;
        $this->_cacheFrontend = $cacheFrontendPool->get(\Magento\Core\Model\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID);
        if ($appState->isInstalled()) {
            $this->_loadTypeStatuses($banAll);
        }
    }

    /**
     * Load statuses (enabled/disabled) of cache types
     *
     * @param bool $forceDisableAll
     */
    private function _loadTypeStatuses($forceDisableAll = false)
    {
        $typeOptions = $this->_cacheFrontend->load(self::CACHE_ID);
        if ($typeOptions !== false) {
            $typeOptions = unserialize($typeOptions);
        } else {
            $typeOptions = $this->_resource->getAllOptions();
            if ($typeOptions !== false) {
                $this->_cacheFrontend->save(serialize($typeOptions), self::CACHE_ID);
            }
        }
        if ($typeOptions) {
            foreach ($typeOptions as $cacheType => $isTypeEnabled) {
                $this->setEnabled($cacheType, $isTypeEnabled && !$forceDisableAll);
            }
        }
    }

    /**
     * Whether a cache type is enabled or not at the moment
     *
     * @param string $cacheType
     * @return bool
     */
    public function isEnabled($cacheType)
    {
        return isset($this->_typeStatuses[$cacheType]) ? (bool)$this->_typeStatuses[$cacheType] : false;
    }

    /**
     * Enable/disable a cache type in run-time
     *
     * @param string $cacheType
     * @param bool $isEnabled
     */
    public function setEnabled($cacheType, $isEnabled)
    {
        $this->_typeStatuses[$cacheType] = (int)$isEnabled;
    }

    /**
     * Save the current statuses (enabled/disabled) of cache types to the persistent storage
     */
    public function persist()
    {
        $this->_resource->saveAllOptions($this->_typeStatuses);
        $this->_cacheFrontend->remove(self::CACHE_ID);
    }
}
