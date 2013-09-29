<?php
/**
 * Catalog attributes configuration data container. Provides catalog attributes configuration data.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute\Config;

class Data extends \Magento\Config\Data
{
    /**
     * @param \Magento\Catalog\Model\Attribute\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     */
    public function __construct(
        \Magento\Catalog\Model\Attribute\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, 'catalog_attributes');
    }
}
