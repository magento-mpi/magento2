<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales configuration data container
 */
namespace Magento\Sales\Model\Config;

class Data extends \Magento\Framework\Config\Data
{
    /**
     * @param \Magento\Sales\Model\Config\Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Sales\Model\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'sales_totals_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}
