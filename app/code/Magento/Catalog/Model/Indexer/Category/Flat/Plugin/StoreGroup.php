<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreGroup extends AbstractStore
{
    /**
     * @param \Magento\Core\Model\Resource\Store\Group $subject
     * @param callable $proceed
     * @param \Magento\Core\Model\AbstractModel $group
     *
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Core\Model\Resource\Store\Group $subject,
        \Closure $proceed,
        \Magento\Core\Model\AbstractModel $group
    ) {
        $needInvalidating = $group->dataHasChangedFor('root_category_id') && !$group->isObjectNew();
        $objectResource = $proceed($group);
        if ($needInvalidating) {
            $this->invalidateIndexer();
        }

        return $objectResource;
    }
}
