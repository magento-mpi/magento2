<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Config;

/**
 * GiftRegistry configuration data container
 */
class Data extends \Magento\Config\Data\Scoped
{
    /**
     * Scope priority loading scheme
     *
     * @var string[]
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param \Magento\GiftRegistry\Model\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\GiftRegistry\Model\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'giftregistry_config_cache'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }
}
