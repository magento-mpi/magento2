<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import;

class Config
    extends \Magento\Config\Data
    implements \Magento\ImportExport\Model\Import\ConfigInterface
{
    /**
     * @param \Magento\ImportExport\Model\Import\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\ImportExport\Model\Import\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'import_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Retrieve import entities configuration
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->get('entities');
    }

    /**
     * Retrieve import product types configuration
     *
     * @return array
     */
    public function getProductTypes()
    {
        return $this->get('productTypes');
    }
}
