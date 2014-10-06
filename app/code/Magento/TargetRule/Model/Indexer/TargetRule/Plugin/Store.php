<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class Store extends AbstractPlugin
{
    /**
     * Before save handler
     *
     * @param \Magento\Store\Model\Resource\Store $subject
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        \Magento\Store\Model\Resource\Store $subject,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        if (!$object->getId() || $object->dataHasChangedFor('group_id')) {
            $this->invalidateIndexers();
        }
    }
}
