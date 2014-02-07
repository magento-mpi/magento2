<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreView extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    protected function validate(\Magento\Core\Model\AbstractModel $store)
    {
        return $store->isObjectNew() || $store->dataHasChangedFor('group_id');
    }
}
