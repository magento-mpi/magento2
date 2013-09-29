<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Export;

class Config
    extends \Magento\Config\Data
    implements \Magento\ImportExport\Model\Export\ConfigInterface
{
    /**
     * @param \Magento\ImportExport\Model\Export\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\ImportExport\Model\Export\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'export_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Retrieve export entities configuration
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->get('entities');
    }

    /**
     * Retrieve export product types configuration
     *
     * @return array
     */
    public function getProductTypes()
    {
        return $this->get('productTypes');
    }

    /**
     * Retrieve export file formats configuration
     *
     * @return array
     */
    public function getFileFormats()
    {
        return $this->get('fileFormats');
    }
}
