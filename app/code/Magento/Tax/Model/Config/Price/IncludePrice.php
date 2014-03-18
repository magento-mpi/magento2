<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Config\Price;

class IncludePrice extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\App\CacheInterface
     */
    protected $cache;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param \Magento\App\CacheInterface $cache
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        \Magento\App\CacheInterface $cache,
        array $data = array()
    ) {
        $this->cache = $cache;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    public function _afterSave()
    {
        parent::_afterSave();
        $this->cache->clean(array('checkout_quote'));
    }
}
