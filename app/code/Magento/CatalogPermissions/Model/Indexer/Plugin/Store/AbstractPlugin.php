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
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer
    ) {
        $this->indexer = $indexer;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Process to invalidate indexer
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function aroundSave(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $needInvalidating = $this->validate($arguments[0]);
        $objectResource = $invocationChain->proceed($arguments);
        if ($needInvalidating) {
            $this->getIndexer()->invalidate();
        }

        return $objectResource;
    }

    /**
     * @param \Magento\Core\Model\AbstractModel $model
     * @return bool
     */
    abstract protected function validate(\Magento\Core\Model\AbstractModel $model);
}
