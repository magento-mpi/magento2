<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Model\Indexer;

use Magento\Store\Model\ScopeInterface;

abstract class AbstractFlatState
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = '';

    /**
     * Flat Is Enabled Config XML Path
     */
    const INDEXER_ENABLED_XML_PATH = '';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var bool
     */
    protected $isAvailable;

    /** @var \Magento\Indexer\Model\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     * @param bool $isAvailable
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry,
        $isAvailable = false
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->indexerRegistry = $indexerRegistry;
        $this->isAvailable = $isAvailable;
    }

    /**
     * Check if Flat Index is enabled
     *
     * @return bool
     */
    public function isFlatEnabled()
    {
        return $this->scopeConfig->isSetFlag(static::INDEXER_ENABLED_XML_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if Flat Index is available for use
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->isAvailable && $this->isFlatEnabled()
            && $this->indexerRegistry->get(static::INDEXER_ID)->isValid();
    }
}
