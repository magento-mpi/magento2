<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin\Store;

abstract class AbstractPlugin
{
    /** @var \Magento\Indexer\Model\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $appConfig;

    /**
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     * @param \Magento\CatalogPermissions\App\ConfigInterface $appConfig
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry,
        \Magento\CatalogPermissions\App\ConfigInterface $appConfig
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->appConfig = $appConfig;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    abstract protected function validate(\Magento\Framework\Model\AbstractModel $model);
}
