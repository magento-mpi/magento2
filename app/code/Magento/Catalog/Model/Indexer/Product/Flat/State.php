<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     * @param \Magento\Catalog\Helper\Product\Flat\Indexer $flatIndexerHelper
     * @param bool $isAvailable
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry,
        \Magento\Catalog\Helper\Product\Flat\Indexer $flatIndexerHelper,
        $isAvailable = false
    ) {
        parent::__construct($scopeConfig, $indexerRegistry, $isAvailable);
        $this->_productFlatIndexerHelper = $flatIndexerHelper;
    }

    /**
     * @return \Magento\Catalog\Helper\Product\Flat\Indexer
     */
    public function getFlatIndexerHelper()
    {
        return $this->_productFlatIndexerHelper;
    }
}
