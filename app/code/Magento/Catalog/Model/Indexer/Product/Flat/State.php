<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Flat;

class State extends \Magento\Catalog\Model\Indexer\AbstractFlatState
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_product_flat';

    /**
     * Flat Is Enabled Config XML Path
     */
    const INDEXER_ENABLED_XML_PATH = 'catalog/frontend/flat_catalog_product';

    /**
     * @var \Magento\Catalog\Helper\Product\Flat\Indexer
     */
    protected $_productFlatIndexerHelper;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $storeConfig
     * @param \Magento\Indexer\Model\IndexerInterface $flatIndexer
     * @param \Magento\Catalog\Helper\Product\Flat\Indexer $flatIndexerHelper
     * @param bool $isAvailable
     */
    public function __construct(
        \Magento\Core\Model\Store\ConfigInterface $storeConfig,
        \Magento\Indexer\Model\IndexerInterface $flatIndexer,
        \Magento\Catalog\Helper\Product\Flat\Indexer $flatIndexerHelper,
        $isAvailable = false
    ) {
        $this->storeConfig = $storeConfig;
        $this->flatIndexer = $flatIndexer;
        $this->_productFlatIndexerHelper = $flatIndexerHelper;
        $this->isAvailable = $isAvailable;
        parent::__construct($storeConfig, $flatIndexer, $isAvailable);
    }

    /**
     * @return \Magento\Catalog\Helper\Product\Flat\Indexer
     */
    public function getFlatIndexerHelper()
    {
        return $this->_productFlatIndexerHelper;
    }
}
