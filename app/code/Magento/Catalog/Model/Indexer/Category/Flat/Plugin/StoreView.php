<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreView extends AbstractStore
{
    /**
     * @param \Magento\Core\Model\Resource\Store $subject
     * @param callable $proceed
     * @param \Magento\Core\Model\AbstractModel $store
     *
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Core\Model\Resource\Store $subject,
        \Closure $proceed,
        \Magento\Core\Model\AbstractModel $store
    ) {
        $needInvalidating = $store->isObjectNew() || $store->dataHasChangedFor('group_id');
        $objectResource = $proceed($store);
        if ($needInvalidating) {
            $this->invalidateIndexer();
        }

        return $objectResource;
    }
}
