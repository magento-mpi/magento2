<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreGroup
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $state;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $state
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $state
    ) {
        $this->indexer = $indexer;
        $this->state = $state;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Validate changes for invalidating indexer
     *
     * @param \Magento\Core\Model\AbstractModel $group
     * @return bool
     */
    protected function validate(\Magento\Core\Model\AbstractModel $group)
    {
        return $group->dataHasChangedFor('root_category_id') && !$group->isObjectNew();
    }
    
    /**
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $subject
     * @param callable $proceed
     * @param \Magento\Core\Model\AbstractModel $group
     *
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Core\Model\Resource\Db\AbstractDb $subject,
        \Closure $proceed,
        \Magento\Core\Model\AbstractModel $group
    ) {
        $needInvalidating = $this->validate($group);
        $objectResource = $proceed($group);
        if ($needInvalidating && $this->state->isFlatEnabled()) {
            $this->getIndexer()->invalidate();
        }

        return $objectResource;
    }
}
