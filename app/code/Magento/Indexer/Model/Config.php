<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

class Config implements ConfigInterface
{
    /**
     * @var Config\Data
     */
    protected $configData;

    /**
     * @param Config\Data $configData
     */
    public function __construct(Config\Data $configData)
    {
        $this->configData = $configData;
    }

    /**
     * Get indexers list
     *
     * @return array[]
     */
    public function getIndexers()
    {
        return $this->configData->get();
    }

    /**
     * Get indexer by ID
     *
     * @param string $indexerId
     * @return array
     */
    public function getIndexer($indexerId)
    {
        return $this->configData->get($indexerId);
    }

    /**
     * @return array
     */
    public function getIndexerIds()
    {
        return array_keys($this->get());
    }
}
