<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductOptions;

class Config extends \Magento\Config\Data implements \Magento\Catalog\Model\ProductOptions\ConfigInterface
{
    /**
     * @param \Magento\Catalog\Model\ProductOptions\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Catalog\Model\ProductOptions\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'product_options_config'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get configuration of product type by name
     *
     * @param string $name
     * @return array
     */
    public function getOption($name)
    {
        return $this->get($name, array());
    }

    /**
     * Get configuration of all registered product types
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }
}
