<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer\Plugin\Store;

class Group extends AbstractPlugin
{
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

    /**
     * Invalidate indexer on store group save
     *
     * @param \Magento\Store\Model\Resource\Group $subject
     * @param callable $proceed
     * @param \Magento\Core\Model\AbstractModel $store
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Store\Model\Resource\Group $subject,
        \Closure $proceed,
        \Magento\Core\Model\AbstractModel $store
    ) {
        $needInvalidating = $this->validate($store);
        $objectResource = $proceed($store);
        if ($needInvalidating && $this->appConfig->isEnabled()) {
            $this->getIndexer()->invalidate();
        }

        return $objectResource;
    }
}
