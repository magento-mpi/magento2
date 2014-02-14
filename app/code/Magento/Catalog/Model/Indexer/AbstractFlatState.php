<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer;

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
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $storeConfig;

    /**
     * @var bool
     */
    protected $isAvailable;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $flatIndexer;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $storeConfig
     * @param \Magento\Indexer\Model\IndexerInterface $flatIndexer
     * @param bool $isAvailable
     */
    public function __construct(
        \Magento\Core\Model\Store\ConfigInterface $storeConfig,
        \Magento\Indexer\Model\IndexerInterface $flatIndexer,
        $isAvailable = false
    ) {
        $this->storeConfig = $storeConfig;
        $this->flatIndexer = $flatIndexer;
        $this->isAvailable = $isAvailable;
    }

    /**
     * Check if Flat Index is enabled
     *
     * @return bool
     */
    public function isFlatEnabled()
    {
        return $this->storeConfig->getConfigFlag(static::INDEXER_ENABLED_XML_PATH);
    }

    /**
     * Check if Flat Index is available for use
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->isAvailable && $this->isFlatEnabled() && $this->getFlatIndexer()->isValid();
    }

    /**
     * Return indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getFlatIndexer()
    {
        if (!$this->flatIndexer->getId()) {
            $this->flatIndexer->load(static::INDEXER_ID);
        }
        return $this->flatIndexer;
    }
}
