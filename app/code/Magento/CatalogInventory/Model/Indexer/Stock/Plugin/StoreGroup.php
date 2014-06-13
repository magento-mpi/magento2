<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Indexer\Stock\Plugin;

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
        if (!$object->getId() || $object->dataHasChangedFor('website_id')) {
            $this->_indexerProcessor->markIndexerAsInvalid();
        }
    }
}
