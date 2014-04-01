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
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     */
    public function __construct(\Magento\Indexer\Model\IndexerInterface $indexer)
    {
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
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * @param \Magento\Model\Resource\Db\AbstractDb $subject
     * @param callable $proceed
     * @param \Magento\Model\AbstractModel $group
     * @return mixed
     */
    public function aroundSave(
        \Magento\Model\Resource\Db\AbstractDb $subject,
        \Closure $proceed,
        \Magento\Model\AbstractModel $group
    ) {
        $needInvalidating = $this->validate($group);
        $objectResource = $proceed($group);
        if ($needInvalidating) {
            $this->getIndexer()->invalidate();
        }

        return $objectResource;
    }

    /**
     * Validate changes for invalidating indexer
     *
     * @param \Magento\Model\AbstractModel $group
     * @return bool
     */
    protected function validate(\Magento\Model\AbstractModel $group)
    {
        return ($group->dataHasChangedFor(
            'website_id'
        ) || $group->dataHasChangedFor(
            'root_category_id'
        )) && !$group->isObjectNew();
    }
}
