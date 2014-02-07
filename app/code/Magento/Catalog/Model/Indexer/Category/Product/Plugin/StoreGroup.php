<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Product\Plugin;

class StoreGroup
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var string
     */
    protected $indexerCode;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param string $indexerCode
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        $indexerCode
    ) {
        $this->indexer = $indexer;
        $this->indexerCode = $indexerCode;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load($this->indexerCode);
        }
        return $this->indexer;
    }

    /**
     * Invalidate indexer
     */
    protected function invalidateIndexer()
    {
        $this->getIndexer()->invalidate();
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
            $this->invalidateIndexer();
        }

        return $objectResource;
    }

    /**
     * Validate changes for invalidating indexer
     *
     * @param \Magento\Core\Model\AbstractModel $group
     * @return bool
     */
    protected function validate(\Magento\Core\Model\AbstractModel $group)
    {
        return ($group->dataHasChangedFor('website_id') || $group->dataHasChangedFor('root_category_id'))
            && !$group->isObjectNew();
    }
}
