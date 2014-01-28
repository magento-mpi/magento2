<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat;

class Config
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_category_flat';

    /**
     * Catalog Category Flat Is Enabled Config
     */
    const XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY = 'catalog/frontend/flat_catalog_category';

    /**
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $storeConfig;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $storeConfig
     */
    public function __construct(\Magento\Core\Model\Store\ConfigInterface $storeConfig)
    {
        $this->storeConfig = $storeConfig;
    }

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @return bool
     */
    public function isFlatEnabled()
    {
        return $this->storeConfig->getConfigFlag(self::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY);
    }
}
