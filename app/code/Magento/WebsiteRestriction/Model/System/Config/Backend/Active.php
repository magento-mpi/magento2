<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cleanup blocks HTML cache
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Backend;

class Active extends \Magento\Core\Model\Config\Value
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
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_websiterestriction_config_active';

    /**
     * Cleanup blocks HTML cache if value has been changed
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cache->clean(array(\Magento\Core\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG));
        }
        return parent::_afterSave();
    }
}
