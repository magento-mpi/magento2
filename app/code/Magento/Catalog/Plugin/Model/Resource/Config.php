<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Plugin\Model\Resource;

class Config
{
    /**#@+
     * Product listing attributes cache ids
     */
    const PRODUCT_LISTING_ATTRIBUTES_CACHE_ID = 'PRODUCT_LISTING_ATTRIBUTES';
    const PRODUCT_LISTING_SORT_BY_ATTRIBUTES_CACHE_ID = 'PRODUCT_LISTING_SORT_BY_ATTRIBUTES';
    /**#@-*/

    /** @var \Magento\Framework\App\CacheInterface */
    protected $cache;

    /** @var bool|null */
    protected $isCacheEnabled = null;

    /**
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState
    ) {
        $this->cache = $cache;
        $this->isCacheEnabled = $cacheState->isEnabled(\Magento\Eav\Model\Cache\Type::TYPE_IDENTIFIER);
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Config $config
     * @param callable $proceed
     * @return array
     */
    public function aroundGetAttributesUsedInListing(\Magento\Catalog\Model\Resource\Config $config, \Closure $proceed)
    {
        $cacheId = self::PRODUCT_LISTING_ATTRIBUTES_CACHE_ID . $config->getEntityTypeId() . '_' . $config->getStoreId();
        if ($this->isCacheEnabled && ($attributes = $this->cache->load($cacheId))) {
            return unserialize($attributes);
        }
        $attributes = $proceed();
        if ($this->isCacheEnabled) {
            $this->cache->save(
                serialize($attributes),
                $cacheId,
                array(
                    \Magento\Eav\Model\Cache\Type::CACHE_TAG,
                    \Magento\Eav\Model\Entity\Attribute::CACHE_TAG
                )
            );
        }
        return $attributes;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Config $config
     * @param callable $proceed
     * @return array
     */
    public function aroundGetAttributesUsedForSortBy(\Magento\Catalog\Model\Resource\Config $config, \Closure $proceed)
    {
        $cacheId = self::PRODUCT_LISTING_SORT_BY_ATTRIBUTES_CACHE_ID . $config->getEntityTypeId() . '_'
            . $config->getStoreId();
        if ($this->isCacheEnabled && ($attributes = $this->cache->load($cacheId))) {
            return unserialize($attributes);
        }
        $attributes = $proceed();
        if ($this->isCacheEnabled) {
            $this->cache->save(
                serialize($attributes),
                $cacheId,
                array(
                    \Magento\Eav\Model\Cache\Type::CACHE_TAG,
                    \Magento\Eav\Model\Entity\Attribute::CACHE_TAG
                )
            );
        }
        return $attributes;
    }
}
