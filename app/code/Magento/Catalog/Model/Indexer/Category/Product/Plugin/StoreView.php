<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Product\Plugin;

class StoreView extends \Magento\Catalog\Model\Indexer\AbstractStore
{
    /**
     * {@inheritdoc}
     */
    protected function validate(\Magento\Core\Model\AbstractModel $store)
    {
        return $store->isObjectNew() || $store->dataHasChangedFor('group_id');
    }
}
