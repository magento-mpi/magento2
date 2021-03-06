<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class StoreGroup extends AbstractPlugin
{
    /**
     * Before save handler
     *
     * @param \Magento\Store\Model\Resource\Group $subject
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        \Magento\Store\Model\Resource\Group $subject,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        if (!$object->getId() || $object->dataHasChangedFor('root_category_id')) {
            $this->invalidateIndexers();
        }
    }
}
